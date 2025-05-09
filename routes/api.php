<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/products', [ProductController::class, 'index']); // todos
    Route::post('/products', [ProductController::class, 'store']); // solo admin
    Route::put('/products/{id}', [ProductController::class, 'update']); // solo admin
    Route::delete('/products/{id}', [ProductController::class, 'destroy']); // solo admin
});
