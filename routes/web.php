<?php

use App\Http\Controllers\EditorController;
use App\Http\Controllers\FinalizationController;
use App\Http\Controllers\YourShopController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

// Main route with auth check
Route::get('/', function () {
    return Auth::check() ? redirect('/yourshop') : redirect('/login');
})->name('mainPage');

// Protected routes group
Route::middleware(['auth', 'verified'])->group(function () {
    // Shop routes
    Route::get('/yourshop', [YourShopController::class, 'index'])->name('yourshop');
    Route::get('/create', function () {
        return Inertia::render('Create');
    })->name('create');
    Route::get('/edit', [EditorController::class, 'index'])->name('editor');

    // Profile routes
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // Item routes
    Route::controller(ItemController::class)->group(function () {
        Route::post('/items', 'store');
        Route::get('/items', 'index');
        Route::get('/items/{id}', 'show');
    });

    // Debug route (if needed in development)
    if (config('app.debug')) {
        Route::get('/debug-auth', function () {
            return Inertia::render('Debug', [
                'authenticated' => Auth::check(),
                'user' => Auth::user(),
                'session_id' => session()->getId()
            ]);
        });
    }
    Route::post('/orders', [OrderController::class, 'store'])->middleware(['auth'])->name('orders.store');
    Route::get('/finalization', [FinalizationController::class, 'index'])->name('finalization');
    Route::get('/order/confirmation/{order}/download', [OrderController::class, 'downloadConfirmation'])
    ->name('order-confirmation-download');
});

require __DIR__.'/auth.php';
