<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\NewsLetterController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\WishlistController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('brands', BrandController::class);
        Route::resource('products', ProductController::class);
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);
        Route::resource('wishlists', WishlistController::class)->only(['index']);
        Route::resource('coupons', CouponController::class);
        Route::get('newsletters', [NewsLetterController::class, 'index'])->name('newsletters.index');
        Route::get('newsletters/download', [NewsLetterController::class, 'download'])->name('newsletters.download');
        Route::delete('newsletters/{id}', [NewsLetterController::class, 'destroy'])->name('newsletters.destroy');
    }); 
