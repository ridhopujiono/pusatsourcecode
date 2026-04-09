<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function createCommerceProduct(array $overrides = []): Product
{
    $slug = $overrides['slug'] ?? Str::slug($overrides['title'] ?? 'produk-'.Str::random(6));
    $sourceCodePath = $overrides['source_code_path'] ?? "products/source-codes/{$slug}.zip";

    if (! array_key_exists('source_code_path', $overrides) || $overrides['source_code_path'] !== null) {
        Storage::disk('local')->put($sourceCodePath, 'demo-source-code');
    }

    return Product::query()->create([
        'title' => 'Produk Demo '.Str::upper(Str::random(4)),
        'slug' => $slug,
        'description' => 'Deskripsi demo produk untuk pengujian flow cart dan checkout.',
        'price' => 'Rp 1.290.000',
        'price_numeric' => 1290000,
        'category' => 'Retail',
        'tech_stack' => ['Laravel', 'MySQL'],
        'features' => ['Fitur A', 'Fitur B'],
        'delivery' => 'Source code + dokumentasi',
        'updated_label' => 'April 2026',
        'is_active' => true,
        'sort_order' => 0,
        'source_code_path' => $sourceCodePath,
        'source_code_original_name' => "{$slug}.zip",
        ...$overrides,
    ]);
}

test('products can be added to cart and shown in cart page', function () {
    Storage::fake('local');

    $product = createCommerceProduct([
        'title' => 'POS Test Checkout',
        'slug' => 'pos-test-checkout',
    ]);

    $this->post(route('cart.store', $product))
        ->assertRedirect(route('cart.index'));

    $this->get(route('cart.index'))
        ->assertOk()
        ->assertSee($product->title)
        ->assertSee('Checkout produk pilihan Anda');
});

test('verified public users can checkout cart items with midtrans snap', function () {
    Storage::fake('local');
    Http::fake([
        'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
            'token' => 'snap-token-123',
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/example',
        ], 200),
    ]);

    config()->set('services.midtrans.client_key', 'client-key-test');
    config()->set('services.midtrans.server_key', 'server-key-test');
    config()->set('services.midtrans.is_production', false);

    $user = User::factory()->create([
        'is_admin' => false,
        'email_verified_at' => now(),
    ]);

    $product = createCommerceProduct([
        'title' => 'ERP Test Checkout',
        'slug' => 'erp-test-checkout',
    ]);

    $this->actingAs($user)
        ->post(route('cart.store', $product))
        ->assertRedirect(route('cart.index'));

    $response = $this->actingAs($user)
        ->postJson(route('checkout.store'));

    $response->assertOk()
        ->assertJsonPath('snap_token', 'snap-token-123');

    $order = Order::query()->with('items')->first();

    expect($order)->not->toBeNull();
    expect($order->user_id)->toBe($user->id);
    expect($order->items)->toHaveCount(1);
    expect($order->items->first()->download_path)->not->toBeNull();
    expect(session()->get('psc_cart_product_ids'))->toBeNull();

    Http::assertSent(function ($request) use ($order) {
        return $request->url() === 'https://app.sandbox.midtrans.com/snap/v1/transactions'
            && $request['transaction_details']['order_id'] === $order->midtrans_order_id
            && $request['transaction_details']['gross_amount'] === $order->total_amount;
    });
});

test('verified public users can checkout a single cart item without clearing the rest of the cart', function () {
    Storage::fake('local');
    Http::fake([
        'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
            'token' => 'snap-token-single-123',
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/example',
        ], 200),
    ]);

    config()->set('services.midtrans.client_key', 'client-key-test');
    config()->set('services.midtrans.server_key', 'server-key-test');
    config()->set('services.midtrans.is_production', false);

    $user = User::factory()->create([
        'is_admin' => false,
        'email_verified_at' => now(),
    ]);

    $firstProduct = createCommerceProduct([
        'title' => 'Produk Checkout Satuan',
        'slug' => 'produk-checkout-satuan',
        'price' => 'Rp 750.000',
        'price_numeric' => 750000,
    ]);

    $secondProduct = createCommerceProduct([
        'title' => 'Produk Tetap di Keranjang',
        'slug' => 'produk-tetap-di-keranjang',
        'price' => 'Rp 1.250.000',
        'price_numeric' => 1250000,
    ]);

    $this->actingAs($user)->post(route('cart.store', $firstProduct));
    $this->actingAs($user)->post(route('cart.store', $secondProduct));

    $response = $this->actingAs($user)->postJson(route('checkout.store'), [
        'product_ids' => [$firstProduct->id],
    ]);

    $response->assertOk()
        ->assertJsonPath('snap_token', 'snap-token-single-123')
        ->assertJsonPath('cart_count', 1)
        ->assertJsonPath('checked_out_product_ids.0', $firstProduct->id);

    $order = Order::query()->with('items')->first();

    expect($order)->not->toBeNull();
    expect($order->total_amount)->toBe($firstProduct->price_numeric);
    expect($order->items)->toHaveCount(1);
    expect($order->items->first()->product_id)->toBe($firstProduct->id);
    expect(session()->get('psc_cart_product_ids'))->toBe([$secondProduct->id]);
});

test('paid order items can be downloaded by the purchaser', function () {
    Storage::fake('local');

    $user = User::factory()->create([
        'is_admin' => false,
        'email_verified_at' => now(),
    ]);

    $product = createCommerceProduct([
        'title' => 'LMS Test Download',
        'slug' => 'lms-test-download',
    ]);

    $order = Order::query()->create([
        'user_id' => $user->id,
        'order_number' => 'PSC-DOWNLOAD-001',
        'midtrans_order_id' => 'PSC-DOWNLOAD-001',
        'customer_name' => $user->name,
        'customer_email' => $user->email,
        'subtotal_amount' => $product->price_numeric,
        'total_amount' => $product->price_numeric,
        'payment_status' => 'paid',
        'paid_at' => now(),
    ]);

    $downloadPath = 'orders/PSC-DOWNLOAD-001/lms-test-download.zip';
    Storage::disk('local')->put($downloadPath, 'paid-order-source-code');

    $item = $order->items()->create([
        'product_id' => $product->id,
        'product_title' => $product->title,
        'product_slug' => $product->slug,
        'price_label' => $product->price,
        'price_numeric' => $product->price_numeric,
        'quantity' => 1,
        'total_numeric' => $product->price_numeric,
        'download_path' => $downloadPath,
        'download_name' => 'lms-test-download.zip',
    ]);

    $this->actingAs($user)
        ->get(route('orders.download', [$order, $item]))
        ->assertOk()
        ->assertDownload('lms-test-download.zip');
});

test('midtrans notification marks pending orders as paid', function () {
    config()->set('services.midtrans.client_key', 'client-key-test');
    config()->set('services.midtrans.server_key', 'server-key-test');
    config()->set('services.midtrans.is_production', false);

    $user = User::factory()->create([
        'is_admin' => false,
        'email_verified_at' => now(),
    ]);

    $order = Order::query()->create([
        'user_id' => $user->id,
        'order_number' => 'PSC-NOTIF-001',
        'midtrans_order_id' => 'PSC-NOTIF-001',
        'customer_name' => $user->name,
        'customer_email' => $user->email,
        'subtotal_amount' => 1290000,
        'total_amount' => 1290000,
        'payment_status' => 'pending',
    ]);

    $grossAmount = '1290000.00';
    $statusCode = '200';

    $payload = [
        'order_id' => $order->midtrans_order_id,
        'status_code' => $statusCode,
        'gross_amount' => $grossAmount,
        'transaction_status' => 'settlement',
        'transaction_id' => 'trx-midtrans-001',
        'payment_type' => 'bank_transfer',
        'signature_key' => hash('sha512', $order->midtrans_order_id.$statusCode.$grossAmount.'server-key-test'),
    ];

    $this->postJson(route('midtrans.notification'), $payload)
        ->assertOk();

    expect($order->fresh()->payment_status)->toBe('paid');
    expect($order->fresh()->midtrans_transaction_id)->toBe('trx-midtrans-001');
});

test('checkout returns actionable message when midtrans rejects server credentials', function () {
    Storage::fake('local');
    Http::fake([
        'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
            'error_messages' => ['Access denied due to unauthorized transaction'],
        ], 401),
    ]);

    config()->set('services.midtrans.client_key', 'client-key-test');
    config()->set('services.midtrans.server_key', 'wrong-server-key');
    config()->set('services.midtrans.is_production', false);

    $user = User::factory()->create([
        'is_admin' => false,
        'email_verified_at' => now(),
    ]);

    $product = createCommerceProduct([
        'title' => 'POS Test Midtrans Unauthorized',
        'slug' => 'pos-test-midtrans-unauthorized',
    ]);

    $this->actingAs($user)
        ->post(route('cart.store', $product));

    $response = $this->actingAs($user)
        ->postJson(route('checkout.store'));

    $response->assertStatus(502)
        ->assertJsonPath('message', 'Midtrans menolak kredensial API. Periksa MIDTRANS_SERVER_KEY dan pastikan key tersebut cocok dengan environment sandbox. MIDTRANS_CLIENT_KEY tidak menyebabkan error 401 ini.');
});
