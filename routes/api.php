<?php

use App\Http\Controllers\BasketController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ScreenshotController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::get('/products/{id}', [ProductController::class, 'getProduct']);
Route::middleware(['auth', 'verified','web'])->group(function () {
    Route::post('/items', [ItemController::class, 'store']);
    Route::get('/items/latest', [ItemController::class, 'getLatest']);
    Route::put('/screenshots', [ScreenshotController::class, 'store']);
    Route::delete('/items/{name}', [ItemController::class, 'destroy']);

    Route::get('/basket', [BasketController::class, 'index']);
    Route::post('/basket', [BasketController::class, 'store']);
    Route::delete('/basket/{id}', [BasketController::class, 'destroy']);
    Route::delete('/basket/clear', [BasketController::class, 'clear']);
    
    Route::get('/latest-order', [OrderController::class, 'getLatestOrder']);
});

