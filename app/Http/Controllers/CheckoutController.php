<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use App\Services\MidtransSnapService;
use App\Services\OrderPaymentService;
use App\Services\ProductSourceCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class CheckoutController extends Controller
{
    public function store(
        Request $request,
        CartService $cartService,
        MidtransSnapService $midtransSnapService,
        ProductSourceCodeService $productSourceCodeService,
    ): JsonResponse {
        $validated = $request->validate([
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'min:1', 'distinct'],
        ]);

        $user = $request->user();
        abort_if($user?->is_admin, 403);

        if (! $midtransSnapService->isConfigured()) {
            return response()->json([
                'message' => 'Midtrans belum dikonfigurasi. Silakan lengkapi MIDTRANS_CLIENT_KEY dan MIDTRANS_SERVER_KEY.',
            ], 422);
        }

        $cartProducts = $cartService->products();

        if ($cartProducts->isEmpty()) {
            return response()->json([
                'message' => 'Keranjang masih kosong.',
            ], 422);
        }

        $selectedProductIds = collect($validated['product_ids'] ?? [])
            ->map(fn (mixed $productId) => (int) $productId)
            ->filter(fn (int $productId) => $productId > 0)
            ->unique()
            ->values();

        $products = $selectedProductIds->isEmpty()
            ? $cartProducts
            : $cartProducts
                ->filter(fn (Product $product) => $selectedProductIds->containsStrict($product->id))
                ->values();

        if ($selectedProductIds->isNotEmpty() && $products->count() !== $selectedProductIds->count()) {
            return response()->json([
                'message' => 'Sebagian produk yang dipilih sudah tidak ada di keranjang. Muat ulang halaman lalu coba lagi.',
            ], 422);
        }

        $unavailableProducts = $products
            ->filter(fn (Product $product) => ! $product->has_source_code_file)
            ->pluck('title')
            ->values();

        if ($unavailableProducts->isNotEmpty()) {
            return response()->json([
                'message' => 'Beberapa produk belum siap untuk checkout: '.$unavailableProducts->implode(', '),
            ], 422);
        }

        $order = DB::transaction(function () use ($user, $products, $productSourceCodeService): Order {
            $orderNumber = $this->generateOrderNumber();
            $subtotalAmount = (int) $products->sum('price_numeric');

            $order = Order::query()->create([
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'midtrans_order_id' => $orderNumber,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'subtotal_amount' => $subtotalAmount,
                'total_amount' => $subtotalAmount,
                'payment_status' => 'pending',
            ]);

            foreach ($products as $product) {
                $downloadSnapshot = $productSourceCodeService->duplicateForOrder(
                    $product->source_code_path,
                    $product->source_code_original_name,
                    $orderNumber,
                    $product->slug,
                );

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_title' => $product->title,
                    'product_slug' => $product->slug,
                    'price_label' => $product->price,
                    'price_numeric' => $product->price_numeric,
                    'quantity' => 1,
                    'total_numeric' => $product->price_numeric,
                    ...$downloadSnapshot,
                ]);
            }

            return $order->load('items');
        });

        try {
            $snap = $midtransSnapService->createSnapTransaction($order);
        } catch (Throwable $throwable) {
            foreach ($order->items as $item) {
                $productSourceCodeService->deleteFile($item->download_path);
            }

            $order->delete();
            report($throwable);

            return response()->json([
                'message' => $throwable->getMessage() ?: 'Gagal membuat transaksi Midtrans. Silakan coba lagi.',
            ], 502);
        }

        $order->update([
            'snap_token' => $snap['token'],
            'snap_redirect_url' => $snap['redirect_url'],
            'snap_payload' => $snap['payload'],
        ]);

        $cartService->removeMany($products->pluck('id'));

        return response()->json([
            'message' => 'Checkout berhasil dibuat.',
            'snap_token' => $order->snap_token,
            'order_number' => $order->order_number,
            'orders_url' => route('orders.index'),
            'refresh_url' => route('orders.refresh', $order),
            'cart_count' => $cartService->count(),
            'checked_out_product_ids' => $products->pluck('id')->values()->all(),
        ]);
    }

    public function pay(
        Request $request,
        Order $order,
        MidtransSnapService $midtransSnapService,
    ): JsonResponse {
        $this->ensureOrderOwner($request, $order);

        if (! $order->can_retry_payment || $order->is_paid) {
            return response()->json([
                'message' => 'Pesanan ini tidak bisa dibayarkan ulang.',
            ], 422);
        }

        try {
            $snap = $midtransSnapService->createSnapTransaction($order->loadMissing('items'));
        } catch (Throwable $throwable) {
            report($throwable);

            return response()->json([
                'message' => $throwable->getMessage() ?: 'Gagal mempersiapkan pembayaran ulang.',
            ], 502);
        }

        $order->update([
            'snap_token' => $snap['token'],
            'snap_redirect_url' => $snap['redirect_url'],
            'snap_payload' => $snap['payload'],
        ]);

        return response()->json([
            'message' => 'Snap token berhasil diperbarui.',
            'snap_token' => $order->snap_token,
            'refresh_url' => route('orders.refresh', $order),
        ]);
    }

    public function refresh(
        Request $request,
        Order $order,
        MidtransSnapService $midtransSnapService,
        OrderPaymentService $orderPaymentService,
    ): JsonResponse {
        $this->ensureOrderOwner($request, $order);

        try {
            $payload = $midtransSnapService->fetchTransactionStatus($order);
        } catch (Throwable $throwable) {
            report($throwable);

            return response()->json([
                'message' => $throwable->getMessage() ?: 'Gagal mengambil status pembayaran terbaru dari Midtrans.',
            ], 502);
        }

        $order = $orderPaymentService->applyMidtransPayload($order, $payload);

        return response()->json([
            'message' => 'Status pembayaran berhasil diperbarui.',
            'payment_status' => $order->payment_status,
            'status_label' => $order->status_label,
            'is_paid' => $order->is_paid,
        ]);
    }

    public function notification(
        Request $request,
        MidtransSnapService $midtransSnapService,
        OrderPaymentService $orderPaymentService,
    ): JsonResponse {
        $payload = $request->all();

        if (! $midtransSnapService->verifyNotificationSignature($payload)) {
            return response()->json([
                'message' => 'Signature Midtrans tidak valid.',
            ], 403);
        }

        $order = Order::query()
            ->where('midtrans_order_id', (string) data_get($payload, 'order_id'))
            ->first();

        if (! $order) {
            return response()->json([
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $orderPaymentService->applyMidtransPayload($order, $payload);

        return response()->json([
            'message' => 'Notifikasi pembayaran diproses.',
        ]);
    }

    private function ensureOrderOwner(Request $request, Order $order): void
    {
        abort_unless($request->user()?->id === $order->user_id, 404);
    }

    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'PSC-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));
        } while (Order::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
