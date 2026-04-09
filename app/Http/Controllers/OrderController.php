<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\MidtransSnapService;
use App\Services\ProductSourceCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderController extends Controller
{
    public function index(Request $request, MidtransSnapService $midtransSnapService): View
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product'])
            ->latest('id')
            ->paginate(10);

        return view('public.orders', [
            'orders' => $orders,
            'midtransConfigured' => $midtransSnapService->isConfigured(),
            'midtransClientKey' => $midtransSnapService->clientKey(),
            'midtransSnapJsUrl' => $midtransSnapService->snapScriptUrl(),
        ]);
    }

    public function download(
        Request $request,
        Order $order,
        OrderItem $item,
        ProductSourceCodeService $productSourceCodeService,
    ): BinaryFileResponse|RedirectResponse {
        abort_unless($request->user()?->id === $order->user_id, 404);
        abort_unless($item->order_id === $order->id, 404);

        if (! $order->is_paid) {
            return redirect()
                ->route('orders.index')
                ->with('error', 'Pesanan belum lunas, file source code belum bisa diunduh.');
        }

        $downloadPath = $item->resolveDownloadPath();
        $downloadName = $item->resolveDownloadName();

        if (! $downloadPath || ! $downloadName) {
            return redirect()
                ->route('orders.index')
                ->with('error', 'File source code belum tersedia untuk item ini.');
        }

        return $productSourceCodeService->download($downloadPath, $downloadName);
    }
}
