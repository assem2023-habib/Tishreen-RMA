<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\TelegramOtpController;
use App\Http\Controllers\Api\V1\Authorization\AuthorizationController;
use App\Http\Controllers\Api\V1\Branche\BranchByCityController;
use App\Http\Controllers\Api\V1\Branche\BranchController;
use App\Http\Controllers\Api\V1\CountryAndCity\CountryController;
use App\Http\Controllers\Api\V1\Parcel\ParcelController;
use App\Http\Controllers\Api\V1\PricingPolicy\PricingPolicyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');

Route::prefix('v1')->group(function () {

    //-----------------------------------Auth-----------------------------
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgetPassword']); // accept email
    Route::post('new-password', [AuthController::class, 'verifyOtpAndResetPassword']); //accept the new password
    Route::post('verify-email', [AuthController::class, 'verifyEmailCode']);
    Route::post('confirm-email-otp', [AuthController::class, 'confirmEmailOtpAndVerifyEmail']);

    //-----------------------------------OTP TELEGRAM----------------------------------

    Route::prefix('telegram')->group(function () {
        Route::post('otp/send', [TelegramOtpController::class, 'send']);
        Route::post('otp/verify', [TelegramOtpController::class, 'verify']);
        Route::post('webhook', [TelegramOtpController::class, 'handle']); // من اجل اعطاء معرف المحادثة ضمن بوت التلغرام
    });

    //-----------------------------------Country And City-------------------------------

    Route::get('/countries', [CountryController::class, 'getCountries']);
    Route::get('/countries/{country_id}/cities', [CountryController::class, 'getCitiesByCountry']);

    //----------------------------------Branches-------------------------------------

    Route::get('/branches', BranchController::class)->name('branches.index');
    Route::get('branches/{cityId}', BranchByCityController::class)->name('branches.show');

    //----------------------------------PricingPolicy---------------------------------

    Route::get('/pricing-policy', PricingPolicyController::class)->name('pricingPolicy.index');


    //----------------------------------------------------------------------------------------------------------------------------

    Route::middleware('auth:api')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);

        //----------------------------------Parcel----------------------------------------

        Route::resource('/parcel', ParcelController::class)
            ->only(['index', 'show', 'store', 'update', 'destroy']);

        //---------------------------------   --------------------------------------------

        Route::resource('/authorization', AuthorizationController::class)
            ->only(['index', 'show', 'store', 'update', 'destroy']);
    });
})->middleware('throttle:6');
