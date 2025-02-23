<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::get('/', function () {
    return response()->json(['message' => 'API is working!']);
});


Route::post('login', [AuthController::class, 'authLogin']);

Route::middleware('auth:api')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('/login/pin', [AuthController::class, 'loginWithPin']);
    Route::put('/update-profile', [AuthController::class, 'updateProfile']);
    Route::put('/update-pin', [AuthController::class, 'updatePin']);
    Route::post('logout', [AuthController::class, 'logout']);
});