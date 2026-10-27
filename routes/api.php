<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductPriceController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login');

Route::middleware(['auth:sanctum','throttle:api'])->group(function(){
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::apiResource('products', ProductController::class);

    Route::get('products/{product}/prices', [ProductPriceController::class, 'index'])->name('products.prices.index');
    Route::post('products/{product}/prices', [ProductPriceController::class, 'store'])->name('products.prices.store');

    Route::get('currencies', [CurrencyController::class, 'index'])->name('currencies.index');
});