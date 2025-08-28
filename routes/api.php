<?php

use App\Http\Controllers\CategoriesProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::apiResource('/product', ProductController::class);
Route::apiResource('/categories', CategoriesProductController::class);

Route::get('generate-token', [TokenController::class, 'generateToken']);
Route::post('/get-data', [TokenController::class, 'getData']);
