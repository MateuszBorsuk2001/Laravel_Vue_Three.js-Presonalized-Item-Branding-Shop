<?php
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ItemController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/items', [ItemController::class, 'store']);
});
Route::get('/products/{id}', [ProductController::class, 'getProduct']);
Route::get('/test', function () {
    return response()->json(['message' => 'dziala']);
});
