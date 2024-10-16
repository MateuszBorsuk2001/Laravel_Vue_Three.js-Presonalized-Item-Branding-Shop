<?php
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductController;

Route::get('/products/{id}', [ProductController::class, 'getProduct']);
Route::get('/test', function () {
    return response()->json(['message' => 'dziala']);
});
