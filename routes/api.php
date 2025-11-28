<?php

use App\Http\Controllers\CategoriesProductController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\adminMiddleware;
use App\Http\Middleware\AdminMiddleware as MiddlewareAdminMiddleware;
use App\Http\Middleware\PelangganMiddleware;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('/product', ProductController::class);
    Route::apiResource('/categories', CategoriesProductController::class);
});

Route::middleware(['auth:sanctum', 'pelanggan'])->group(function () {
    Route::post('/pelanggan/penyewaan', [PelangganController::class, 'store']);
    Route::get('/penyewaan/riwayat', [PelangganController::class, 'riwayat']);
    Route::get('/penyewaan/{id}', [PelangganController::class, 'show']);
    Route::get('/pelanggan/product', [PelangganController::class, 'Melihat']);
});

Route::post('/pelanggan', [UserController::class, 'registerPelanggan']);
Route::post('/admin', [UserController::class, 'registerAdmin']);

Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);
