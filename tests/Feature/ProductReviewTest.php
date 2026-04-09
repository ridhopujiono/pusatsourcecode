<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Support\Str;

function createReviewableProduct(array $overrides = []): Product
{
    $slug = $overrides['slug'] ?? Str::slug($overrides['title'] ?? 'produk-ulasan-'.Str::random(6));

    return Product::query()->create([
        'title' => 'Produk Ulasan '.Str::upper(Str::random(4)),
        'slug' => $slug,
        'description' => 'Produk demo untuk pengujian rating dan komentar.',
        'price' => 'Rp 990.000',
        'price_numeric' => 990000,
        'category' => 'Retail',
        'tech_stack' => ['Laravel', 'MySQL'],
        'features' => ['Review pembeli', 'Rating bintang'],
        'delivery' => 'Source code + dokumentasi',
        'updated_label' => 'April 2026',
        'is_active' => true,
        'sort_order' => 0,
        ...$overrides,
    ]);
}

function createPaidOrderItem(User $user, Product $product): Order
{
    $orderNumber = 'PSC-REVIEW-'.Str::upper(Str::random(8));

    $order = Order::query()->create([
        'user_id' => $user->id,
        'order_number' => $orderNumber,
        'midtrans_order_id' => $orderNumber,
        'customer_name' => $user->name,
        'customer_email' => $user->email,
        'subtotal_amount' => $product->price_numeric,
        'total_amount' => $product->price_numeric,
        'payment_status' => 'paid',
        'paid_at' => now(),
    ]);

    $order->items()->create([
        'product_id' => $product->id,
        'product_title' => $product->title,
        'product_slug' => $product->slug,
        'price_label' => $product->price,
        'price_numeric' => $product->price_numeric,
        'quantity' => 1,
        'total_numeric' => $product->price_numeric,
    ]);

    return $order;
}

test('paid purchasers can create and update a product review', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $product = createReviewableProduct([
        'title' => 'CRM Review Test',
        'slug' => 'crm-review-test',
    ]);

    createPaidOrderItem($user, $product);

    $this->actingAs($user)
        ->post(route('product.reviews.store', ['product' => $product->slug]), [
            'rating' => 5,
            'comment' => 'Produk sangat rapi dan mudah di-deploy.',
        ])
        ->assertRedirect(route('product.show', $product->slug).'#ulasan-produk');

    $review = ProductReview::query()->first();

    expect($review)->not->toBeNull();
    expect($review->rating)->toBe(5);
    expect($review->comment)->toBe('Produk sangat rapi dan mudah di-deploy.');

    $this->actingAs($user)
        ->post(route('product.reviews.store', ['product' => $product->slug]), [
            'rating' => 4,
            'comment' => 'Sudah saya update setelah dipakai beberapa hari.',
        ])
        ->assertRedirect(route('product.show', $product->slug).'#ulasan-produk');

    expect(ProductReview::query()->count())->toBe(1);
    expect($review->fresh()->rating)->toBe(4);
    expect($review->fresh()->comment)->toBe('Sudah saya update setelah dipakai beberapa hari.');
});

test('users without a paid order cannot submit a product review', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $product = createReviewableProduct([
        'title' => 'POS Review Lock',
        'slug' => 'pos-review-lock',
    ]);

    $this->actingAs($user)
        ->from(route('product.show', $product->slug))
        ->post(route('product.reviews.store', ['product' => $product->slug]), [
            'rating' => 3,
            'comment' => 'Saya mencoba submit review tanpa pembelian.',
        ])
        ->assertRedirect(route('product.show', $product->slug).'#ulasan-produk');

    expect(ProductReview::query()->count())->toBe(0);
});

test('product detail page shows public review summary and comments', function () {
    $firstUser = User::factory()->create([
        'name' => 'Rina Pelanggan',
        'email_verified_at' => now(),
    ]);
    $secondUser = User::factory()->create([
        'name' => 'Budi Startup',
        'email_verified_at' => now(),
    ]);

    $product = createReviewableProduct([
        'title' => 'ERP Review Showcase',
        'slug' => 'erp-review-showcase',
    ]);

    createPaidOrderItem($firstUser, $product);
    createPaidOrderItem($secondUser, $product);

    ProductReview::query()->create([
        'product_id' => $product->id,
        'user_id' => $firstUser->id,
        'rating' => 5,
        'comment' => 'Dashboard adminnya membantu operasional harian.',
    ]);

    ProductReview::query()->create([
        'product_id' => $product->id,
        'user_id' => $secondUser->id,
        'rating' => 4,
        'comment' => 'Integrasi dan struktur foldernya enak dipahami.',
    ]);

    $this->get(route('product.show', $product->slug))
        ->assertOk()
        ->assertSee('4.5')
        ->assertSee('2 ulasan terverifikasi')
        ->assertSee('Rina Pelanggan')
        ->assertSee('Budi Startup')
        ->assertSee('Dashboard adminnya membantu operasional harian.')
        ->assertSee('Integrasi dan struktur foldernya enak dipahami.');
});
