<?php

use App\Http\Controllers\UserController;
use App\Http\Middleware\TokenVerificationMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/user-registration',[UserController::class,'UserRegistration']);
Route::Post('/user-login',[UserController::class,'UserLogin']);
Route::Post('/send-otp',[UserController::class,'SendOTPCode']);
Route::Post('/verify-otp',[UserController::class,'VerifyOTP']);
Route::Post('/reset-password',[UserController::class,'ResetPassword'])->middleware(TokenVerificationMiddleware::class);
