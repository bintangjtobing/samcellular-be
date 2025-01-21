<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\BrandApiController;
use App\Http\Controllers\Api\TransactionApiController;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route untuk mengambil semua produk
Route::get('/products', [ProductApiController::class, 'index']);
Route::get('/products/{id}', [ProductApiController::class, 'show']);

Route::get('categories', [CategoryApiController::class, 'index']);
Route::get('categories/dropdown', [CategoryApiController::class, 'dropdown']);

Route::get('brands', [BrandApiController::class, 'index']);
Route::get('brands/dropdown', [BrandApiController::class, 'dropdown']);

Route::post('/transactions', [TransactionApiController::class, 'store']);