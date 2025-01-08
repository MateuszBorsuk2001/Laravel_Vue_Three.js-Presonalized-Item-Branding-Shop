<?php
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ItemController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::get('/products/{id}', [ProductController::class, 'getProduct']);
Route::middleware(['auth', 'verified','web'])->group(function () {
    Route::post('/items', [ItemController::class, 'store']);
    Route::get('/items/latest', [ItemController::class, 'getLatest']);
});
