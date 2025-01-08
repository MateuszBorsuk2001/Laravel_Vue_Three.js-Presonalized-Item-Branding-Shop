<?php
use App\Http\Controllers\YourShopController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/yourshop');
    }
    return redirect('/login'); 
})->name('mainPage');

Route::middleware(['auth', 'verified','web'])->group(function () {
    Route::get('/yourshop', function () {
        return Inertia::render('YourShop');
    })->name('yourshop');

    Route::get('/create', function () {
        return Inertia::render('Create');
    })->name('create');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/items', [ItemController::class, 'store']);
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/{id}', [ItemController::class, 'show']);

    Route::get('/debug-auth', function () {
        return Inertia::render('Debug', [
            'authenticated' => Auth::check(),
            'user' => Auth::user(),
            'session_id' => session()->getId()
        ]);
    });
});

Route::get('/yourshop', [YourShopController::class, 'index'])->name('yourshop');

require __DIR__.'/auth.php';



