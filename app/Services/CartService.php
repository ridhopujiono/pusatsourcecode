<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    private const SESSION_KEY = 'psc_cart_product_ids';

    public function products(): Collection
    {
        $productIds = $this->productIds();

        if ($productIds === []) {
            return collect();
        }

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $orderedProducts = collect($productIds)
            ->map(fn (int $productId) => $products->get($productId))
            ->filter()
            ->values();

        if ($orderedProducts->count() !== count($productIds)) {
            $this->replace($orderedProducts->pluck('id')->all());
        }

        return $orderedProducts;
    }

    public function add(Product $product): bool
    {
        $productIds = $this->productIds();

        if (in_array($product->id, $productIds, true)) {
            return false;
        }

        $productIds[] = $product->id;
        $this->replace($productIds);

        return true;
    }

    public function remove(Product|int $product): bool
    {
        $productId = $product instanceof Product ? $product->id : $product;
        $productIds = $this->productIds();

        if (! in_array($productId, $productIds, true)) {
            return false;
        }

        $this->replace(array_values(array_filter(
            $productIds,
            fn (int $currentProductId) => $currentProductId !== $productId,
        )));

        return true;
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function removeMany(iterable $products): int
    {
        $productIdsToRemove = collect($products)
            ->map(fn (mixed $product) => $product instanceof Product ? $product->id : (int) $product)
            ->filter(fn (int $productId) => $productId > 0)
            ->unique()
            ->values()
            ->all();

        if ($productIdsToRemove === []) {
            return 0;
        }

        $currentProductIds = $this->productIds();
        $remainingProductIds = array_values(array_filter(
            $currentProductIds,
            fn (int $productId) => ! in_array($productId, $productIdsToRemove, true),
        ));

        $removedCount = count($currentProductIds) - count($remainingProductIds);

        if ($removedCount === 0) {
            return 0;
        }

        if ($remainingProductIds === []) {
            $this->clear();

            return $removedCount;
        }

        $this->replace($remainingProductIds);

        return $removedCount;
    }

    public function count(): int
    {
        return count($this->productIds());
    }

    public function subtotal(): int
    {
        return (int) $this->products()->sum('price_numeric');
    }

    private function productIds(): array
    {
        return collect(session()->get(self::SESSION_KEY, []))
            ->map(fn (mixed $productId) => (int) $productId)
            ->filter(fn (int $productId) => $productId > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function replace(array $productIds): void
    {
        session()->put(self::SESSION_KEY, array_values(array_map('intval', $productIds)));
    }
}
