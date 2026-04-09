<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang/{product}', [CartController::class, 'store'])->name('cart.store');
Route::delete('/keranjang/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::delete('/keranjang', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/produk/{slug}', [PublicController::class, 'show'])->name('product.show');
Route::get('/legal', [PublicController::class, 'legal'])->name('legal');
Route::post('/midtrans/notification', [CheckoutController::class, 'notification'])->name('midtrans.notification');
Route::post('/produk/{product:slug}/ulasan', [ProductReviewController::class, 'store'])
    ->middleware('auth')
    ->name('product.reviews.store');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/pesanan-saya', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/pesanan-saya/{order}/bayar', [CheckoutController::class, 'pay'])->name('orders.pay');
    Route::post('/pesanan-saya/{order}/refresh-payment', [CheckoutController::class, 'refresh'])->name('orders.refresh');
    Route::get('/pesanan-saya/{order}/items/{item}/unduh', [OrderController::class, 'download'])->name('orders.download');
});

Route::get('/dashboard', fn () => redirect()->route('admin.products.index'))
    ->middleware(['auth', 'is_admin'])
    ->name('dashboard');

Route::prefix('admin')->middleware(['auth', 'is_admin'])->name('admin.')->group(function (): void {
    Route::get('/', fn () => redirect()->route('admin.products.index'))->name('home');
    Route::resource('products', ProductController::class)->except(['show']);
    Route::post('products/{product}/screenshots', [ProductController::class, 'storeScreenshot'])->name('products.screenshots.store');
    Route::post('products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');
    Route::post('products/reorder', [ProductController::class, 'reorder'])->name('products.reorder');
});

require __DIR__.'/auth.php';
