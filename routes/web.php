<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Customer\CustomerProfileController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\OrderSuccessController;
use App\Http\Controllers\Storefront\CouponController;
use App\Http\Controllers\Storefront\PaymentResultController;
use App\Http\Controllers\Storefront\ProductController;
use App\Http\Controllers\Storefront\ShopController;
use App\Http\Controllers\Storefront\SslcommerzCallbackController;
use App\Http\Controllers\Storefront\StripeCallbackController;
use App\Http\Controllers\Storefront\StripeWebhookController;
use App\Http\Controllers\Storefront\WishlistController;
use App\Http\Controllers\Storefront\Api\SearchSuggestionController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/shop', ShopController::class)->name('shop.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('shop.products.show');

// Search
Route::get('/api/search-suggestions', SearchSuggestionController::class)
    ->name('api.search-suggestions');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('shop.cart');
Route::post('/cart', [CartController::class, 'store'])->name('shop.cart.store');
Route::patch('/cart/{productId}', [CartController::class, 'update'])->name('shop.cart.update');
Route::delete('/cart', [CartController::class, 'clear'])->name('shop.cart.clear');
Route::delete('/cart/{productId}', [CartController::class, 'destroy'])->name('shop.cart.destroy');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('shop.wishlist');
Route::middleware('auth')->group(function () {
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('shop.wishlist.store');
    Route::delete('/wishlist', [WishlistController::class, 'clear'])->name('shop.wishlist.clear');
    Route::delete('/wishlist/{productId}', [WishlistController::class, 'destroy'])->name('shop.wishlist.destroy');
});
Route::get('/checkout', [CheckoutController::class, 'index'])->name('shop.checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('shop.checkout.store');
Route::get('/orders/success', OrderSuccessController::class)->name('shop.orders.success');

Route::post('checkout/coupon', [CouponController::class, 'apply'])
    ->name('shop.checkout.coupon.apply');
Route::delete('checkout/coupon', [CouponController::class, 'remove'])
    ->name('shop.checkout.coupon.remove');

Route::get('/orders/payment/success', [PaymentResultController::class, 'success'])->name('shop.payments.success');
Route::get('/orders/payment/failed', [PaymentResultController::class, 'failed'])->name('shop.payments.failed');
Route::get('/orders/payment/cancelled', [PaymentResultController::class, 'cancelled'])->name('shop.payments.cancelled');

Route::post('/payments/sslcommerz/success', [SslcommerzCallbackController::class, 'success'])->name('shop.payments.sslcommerz.success');
Route::post('/payments/sslcommerz/failure', [SslcommerzCallbackController::class, 'failure'])->name('shop.payments.sslcommerz.failure');
Route::post('/payments/sslcommerz/cancel', [SslcommerzCallbackController::class, 'cancel'])->name('shop.payments.sslcommerz.cancel');

// Stripe redirect callbacks (GET — from Stripe hosted checkout)
Route::get('/payments/stripe/success', [StripeCallbackController::class, 'success'])->name('shop.payments.stripe.success');
Route::get('/payments/stripe/cancel', [StripeCallbackController::class, 'cancel'])->name('shop.payments.stripe.cancel');

// Stripe webhook — must be OUTSIDE CSRF middleware (raw POST from Stripe servers)
Route::post('/payments/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('shop.payments.stripe.webhook')
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardRedirectController::class)->name('dashboard');
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
});

Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile/edit', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
});

require __DIR__ . '/settings.php';
