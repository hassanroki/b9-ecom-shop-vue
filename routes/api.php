<?php

use App\Http\Controllers\Api\SslCommerzController;
use Illuminate\Support\Facades\Route;

Route::post('/sslcommerz/initiate', [SslCommerzController::class, 'initiate'])->name('api.sslcommerz.initiate');
Route::post('/sslcommerz/success', [SslCommerzController::class, 'success'])->name('sslc.success');
Route::post('/sslcommerz/failure', [SslCommerzController::class, 'failure'])->name('sslc.failure');
Route::post('/sslcommerz/cancel', [SslCommerzController::class, 'cancel'])->name('sslc.cancel');
Route::post('/sslcommerz/ipn', [SslCommerzController::class, 'ipn'])->name('sslc.ipn');
