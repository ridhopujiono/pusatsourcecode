<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use App\Services\MidtransSnapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(CartService $cartService, MidtransSnapService $midtransSnapService): View
    {
        $products = $cartService->products();

        return view('public.cart', [
            'products' => $products,
            'subtotalAmount' => $cartService->subtotal(),
            'midtransConfigured' => $midtransSnapService->isConfigured(),
            'midtransClientKey' => $midtransSnapService->clientKey(),
            'midtransSnapJsUrl' => $midtransSnapService->snapScriptUrl(),
        ]);
    }

    public function store(Request $request, Product $product, CartService $cartService): JsonResponse|RedirectResponse
    {
        abort_unless($product->is_active, 404);

        if (! $product->has_source_code_file) {
            return $this->respond(
                $request,
                'Produk ini belum siap untuk checkout otomatis karena file source code belum diunggah.',
                false,
                422,
                $cartService,
            );
        }

        $added = $cartService->add($product);
        $message = $added
            ? 'Produk berhasil ditambahkan ke keranjang.'
            : 'Produk ini sudah ada di keranjang.';

        return $this->respond($request, $message, $added, 200, $cartService);
    }

    public function destroy(Request $request, Product $product, CartService $cartService): JsonResponse|RedirectResponse
    {
        $removed = $cartService->remove($product);
        $message = $removed
            ? 'Produk berhasil dihapus dari keranjang.'
            : 'Produk tidak ditemukan di keranjang.';

        return $this->respond($request, $message, $removed, 200, $cartService);
    }

    public function clear(Request $request, CartService $cartService): JsonResponse|RedirectResponse
    {
        $cartService->clear();

        return $this->respond($request, 'Keranjang berhasil dikosongkan.', true, 200, $cartService);
    }

    private function respond(
        Request $request,
        string $message,
        bool $success,
        int $status,
        CartService $cartService,
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'success' => $success,
                'cart_count' => $cartService->count(),
            ], $status);
        }

        return redirect()
            ->route('cart.index')
            ->with($success ? 'success' : 'error', $message);
    }
}
