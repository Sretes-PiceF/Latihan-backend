<?php

use App\Http\Controllers\CategoriesProductController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::apiResource('/product', ProductController::class);
Route::apiResource('/categories', CategoriesProductController::class);
