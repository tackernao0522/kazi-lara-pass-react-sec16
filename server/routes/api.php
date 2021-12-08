<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgetPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Login Routes
Route::post('/login', [AuthController::class, 'login']);

// Register Routes
Route::post('/register', [AuthController::class, 'register']);

// Forget Password Routes
Route::post('/forgetpassword', [ForgetPasswordController::class, 'forgetPassword']);
