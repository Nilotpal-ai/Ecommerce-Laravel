<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Shop\CatalogController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\StripePaymentController;
use App\Http\Controllers\Webhook\StripeWebhookController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Customer\OrderHistoryController;
use App\Http\Controllers\Admin\DashboardController;

// Customer Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/orders', [OrderHistoryController::class, 'index'])->name('customer.orders.index');
    Route::get('/orders/{order}', [OrderHistoryController::class, 'show'])->name('customer.orders.show');
});

// Admin Protected Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
});

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('/success/{orderNumber}', [CheckoutController::class, 'success'])->name('success');

    // Stripe Flow
    // 1. Static Stripe return endpoints (MUST be defined first)
    Route::get('/stripe/success', [StripePaymentController::class, 'success'])->name('stripe.success');
    Route::get('/stripe/cancel/{orderNumber}', [StripePaymentController::class, 'cancel'])->name('stripe.cancel');

    // 2. Stripe payment redirect endpoint
    Route::get('/payment/{order}/stripe', [StripePaymentController::class, 'checkout'])->name('payment.stripe');

    // 3. Dynamic order confirmation endpoint (MUST be defined after static endpoints)
    Route::get('/success/{orderNumber}', [CheckoutController::class, 'success'])->name('success');
});

// Stripe Webhook Endpoint (POST)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'store'])->name('add');
    Route::patch('/update/{product}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{product}', [CartController::class, 'destroy'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
});

// Public Storefront Routes
Route::get('/', [CatalogController::class, 'home'])->name('home');
Route::get('/shop', [CatalogController::class, 'index'])->name('shop.index');
Route::get('/category/{category:slug}', [CatalogController::class, 'category'])->name('shop.category');
Route::get('/product/{product:slug}', [CatalogController::class, 'show'])->name('shop.product.show');


Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('social.redirect')
    ->where('provider', 'google|github|facebook|linkedin');

Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('social.callback')
    ->where('provider', 'google|github|facebook|linkedin');

// Intercept /dashboard: Send everyone directly to /shop
Route::get('/dashboard', function () {
    return redirect()->route('shop.index');
})->middleware(['auth'])->name('dashboard');

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('shop.index');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('products', ProductController::class);
    Route::delete('product-images/{image}', [ProductController::class, 'deleteImage'])->name('product-images.destroy');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
});
