<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductPriceController;
use App\Http\Controllers\Api\CurrencyController;
use Illuminate\Support\Facades\Route;

Route::apiResource('products', ProductController::class);

Route::get('products/{product}/prices', [ProductPriceController::class, 'index'])->name('products.prices.index');
Route::post('products/{product}/prices', [ProductPriceController::class, 'store'])->name('products.prices.store');

Route::get('currencies', [CurrencyController::class, 'index'])->name('currencies.index');