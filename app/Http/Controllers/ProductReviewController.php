<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductReviewRequest;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;

class ProductReviewController extends Controller
{
    public function store(StoreProductReviewRequest $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $user = $request->user();

        $hasPaidOrder = OrderItem::query()
            ->where('product_id', $product->id)
            ->whereHas('order', function ($query) use ($user): void {
                $query
                    ->where('user_id', $user->id)
                    ->where('payment_status', 'paid');
            })
            ->exists();

        if (! $hasPaidOrder) {
            return redirect()
                ->to(route('product.show', $product->slug).'#ulasan-produk')
                ->with('error', 'Ulasan hanya tersedia untuk akun yang sudah membeli dan melunasi produk ini.');
        }

        $review = $product->reviews()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'rating' => (int) $request->integer('rating'),
                'comment' => trim((string) $request->input('comment')),
            ],
        );

        return redirect()
            ->to(route('product.show', $product->slug).'#ulasan-produk')
            ->with('success', $review->wasRecentlyCreated ? 'Ulasan Anda berhasil dikirim.' : 'Ulasan Anda berhasil diperbarui.');
    }
}
