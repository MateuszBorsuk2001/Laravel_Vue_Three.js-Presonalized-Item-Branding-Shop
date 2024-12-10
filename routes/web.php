<?php
use App\Http\Controllers\YourShopController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    if (Auth::check()) {
        return Inertia::render('YourShop');
    }
    return redirect('/login'); 
})->name('mainPage');

Route::get('/yourshop', function () {
    return Inertia::render('YourShop');
})->middleware(['auth', 'verified'])->name('yourshop');

Route::get('/create', function () {
    return Inertia::render('Create');
})->middleware(['auth', 'verified'])->name('create');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/items', [ItemController::class, 'store']);
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/{id}', [ItemController::class, 'show']);
});

Route::get('/yourshop', [YourShopController::class, 'index'])->name('yourshop');

require __DIR__.'/auth.php';



