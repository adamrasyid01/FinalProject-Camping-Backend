<?php

use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth API

Route::name('admin.')->group(function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'register']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [UserController::class, 'getCurrentUser']);
        Route::post('/logout', [UserController::class, 'logout']);
    });
});

