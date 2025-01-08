<?php
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ItemController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::get('/products/{id}', [ProductController::class, 'getProduct']);

Route::middleware([EnsureFrontendRequestsAreStateful::class])
     ->group(function () {
         Route::post('/items', [ItemController::class, 'store']);
        //  Route::get('/user', function () {
        //     $user = auth()->user();
        //     return response()->json([
        //         'id' => $user->id,
        //         'name' => $user->name,
        //         'email' => $user->email
        //     ]);
        // });
     });
     Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/user', function () {
            $user = auth()->user();
            return response()->json([
                'auth_check' => auth()->check(),
                'session_id' => session()->getId(),
                'user' => $user
            ]);
        });
    });
    Route::get('/auth-status', function () {
        return response()->json([
            'authenticated' => auth()->check(),
            'user' => auth()->user(),
            'session' => session()->all()
        ]);
    });