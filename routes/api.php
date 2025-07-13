<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\CountryAndCity\CountryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::prefix('v1')->group(function () {

    //-----------------------------------Auth-----------------------------
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgetPassword']); // accept email
    Route::post('new-password', [AuthController::class, 'verifyOtpAndResetPassword']); //accept the new password
    Route::post('verify-email', [AuthController::class, 'verifyEmailCode']);
    Route::post('confirm-email-otp', [AuthController::class, 'confirmEmailOtpAndVerifyEmail']);

    //-----------------------------------Country And City-------------------------------

    Route::get('/countries', [CountryController::class, 'getCountries']);
    Route::get('/countries/{country_id}/cities', [CountryController::class, 'getCitiesByCountry']);



    Route::middleware('auth:api')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });
});
