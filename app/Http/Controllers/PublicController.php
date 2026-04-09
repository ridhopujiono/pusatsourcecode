<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $productsData = $products->map(fn (Product $product) => [
            'id' => $product->id,
            'title' => $product->title,
            'slug' => $product->slug,
            'description' => $product->description,
            'category' => $product->category,
            'tech_stack' => $product->tech_stack,
            'features' => $product->features,
            'price' => $product->price,
            'updated_label' => $product->updated_label,
            'whatsapp_url' => $product->whatsapp_url,
            'list_thumbnail_url' => $product->list_thumbnail_url,
            'has_source_code_file' => $product->has_source_code_file,
        ])->values();

        $categories = $products
            ->pluck('category')
            ->unique()
            ->sort()
            ->values();

        $techStacks = $products
            ->pluck('tech_stack')
            ->flatten()
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('public.home', compact('products', 'productsData', 'categories', 'techStacks'));
    }

    public function show(string $slug): View
    {
        $product = Product::query()
            ->with([
                'screenshots',
                'reviews' => fn ($query) => $query->with('user:id,name'),
            ])
            ->withCount('reviews')
            ->withAvg('reviews as reviews_avg_rating', 'rating')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $user = auth()->user();
        $canSubmitReview = false;
        $userReview = null;

        if ($user) {
            $canSubmitReview = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', function ($query) use ($user): void {
                    $query
                        ->where('user_id', $user->id)
                        ->where('payment_status', 'paid');
                })
                ->exists();

            if ($canSubmitReview) {
                $userReview = $product->reviews->firstWhere('user_id', $user->id);
            }
        }

        return view('public.product-detail', compact('product', 'canSubmitReview', 'userReview'));
    }

    public function legal(): View
    {
        return view('public.legal');
    }
}
