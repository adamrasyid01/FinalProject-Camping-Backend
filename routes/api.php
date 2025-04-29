<?php

use App\Http\Controllers\API\AhpResultController;
use App\Http\Controllers\API\BookmarkController;
use App\Http\Controllers\API\CampingLocationController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserPreferenceCriteriaController;
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

Route::name('auth.')->group(function () {
    Route::post('/login', [UserController::class, 'login'])->name('login');
    Route::post('/register', [UserController::class, 'register'])->name('regoster');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [UserController::class, 'getCurrentUser']);
        Route::post('/logout', [UserController::class, 'logout']);
    });
});

// CampingLocation API
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/home', [CampingLocationController::class, 'index']);
});

// UserPreferenceCriteria API
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user-preference-criteria', [UserPreferenceCriteriaController::class, 'saveUserPreferenceCriteria']);
});

// Get CampingSite by ID CampingLocation API
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/camping-locations/{id}/sites', [CampingLocationController::class, 'getLocationWithSites']);
});

// Bookmark API
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/bookmarks', [BookmarkController::class, 'index']);
    Route::post('/bookmarks', [BookmarkController::class, 'store']);
    Route::delete('/bookmarks/{camping_site_id}', [BookmarkController::class, 'destroy']);
});

// AhpResult API
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/ahp-results', [AhpResultController::class, 'getAllAHP']);
});