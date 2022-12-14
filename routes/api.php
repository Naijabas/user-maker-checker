<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RequestController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// public routes
Route::post('/login', [AuthController::class, 'login'])->name('login.api');
Route::post('/register', [AuthController::class, 'register'])->name('register.api');


Route::middleware('auth:sanctum')->group(function () {

    //Routes for users
    Route::apiResource('users', UsersController::class);

    //Routes for all request
    Route::apiResource('requests', RequestController::class);

    Route::post('/request/{id}/{type}', [RequestController::class, 'review']);

    // Protected Routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout.api');
});

