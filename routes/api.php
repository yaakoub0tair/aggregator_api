<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SourceController;

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);
});

// Public routes
Route::get('/articles',        [ArticleController::class, 'index']);
Route::get('/articles/{slug}', [ArticleController::class, 'show']);
Route::get('/search',          [ArticleController::class, 'search']);
Route::get('/categories',      [CategoryController::class, 'index']);
Route::get('/sources',         [SourceController::class, 'index']);

// Admin protected routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/articles',         [ArticleController::class, 'store']);
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy']);

    Route::post('/categories',              [CategoryController::class, 'store']);
    Route::put('/categories/{category}',    [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    Route::post('/sources',           [SourceController::class, 'store']);
    Route::put('/sources/{source}',   [SourceController::class, 'update']);
    Route::delete('/sources/{source}',[SourceController::class, 'destroy']);
});