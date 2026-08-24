<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use Illuminate\Support\Facades\Route;

// ---------- Endpoints públicos ----------
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // logout y me requieren un token válido para saber a qué usuario pertenecen
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// ---------- Endpoints protegidos (requieren "Authorization: Bearer {token}") ----------
Route::middleware('auth:api')->group(function () {
    Route::get('books', [BookController::class, 'index']);
    Route::get('books/{book}', [BookController::class, 'show']);
    Route::post('books', [BookController::class, 'store']);
    Route::put('books/{book}', [BookController::class, 'update']);
    Route::patch('books/{book}', [BookController::class, 'update']);
    Route::delete('books/{book}', [BookController::class, 'destroy']);
});
