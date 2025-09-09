<?php

use App\Http\Controllers\Api\V1\Appointment\AppointmentController;
use App\Http\Controllers\Api\V1\Notification\NotificationController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\TelegramOtpController;
use App\Http\Controllers\Api\V1\Authorization\AuthorizationController;
use App\Http\Controllers\Api\V1\Branche\BranchByCityController;
use App\Http\Controllers\Api\V1\Branche\BranchController;
use App\Http\Controllers\Api\V1\Branche\GetBranchById;
use App\Http\Controllers\Api\V1\BranchRoute\BranchRouteController;
use App\Http\Controllers\Api\V1\CountryAndCity\CountryController;
use App\Http\Controllers\Api\V1\Day\DaysController;
use App\Http\Controllers\Api\V1\Parcel\ParcelController;
use App\Http\Controllers\Api\V1\PricingPolicy\PricingPolicyController;
use App\Http\Controllers\Api\V1\Rates\RatesController;
use App\Http\Controllers\Api\V1\Users\UsersController;
use App\Http\Controllers\BroadcastController;
use App\Models\BranchRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    //----------------------------------Appointment-------------------------------------
    Route::get('/get-getCalender/{tracking_number}', [AppointmentController::class, 'getCalenderByParcelTrackingNumber']);
    Route::post('/book-appointment', [AppointmentController::class, 'bookAppointment']);

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
    Route::get('/branches/{cityId}', BranchByCityController::class)->name('branches.show');
    Route::get('/branches/{id}/detail', GetBranchById::class);

    //----------------------------------PricingPolicy---------------------------------

    Route::get('/pricing-policy', PricingPolicyController::class)->name('pricingPolicy.index');

    //-------------------------------BranchRoute-----------------------------------------------

    Route::get('/routes', BranchRouteController::class);
    Route::get('/routes/{day}', [BranchRouteController::class, 'showRoutesByDay']);

    //------------------------------Day--------------------------------------------------------

    Route::get('/days', DaysController::class);

    //----------------------------------------------------------------------------------------------------------------------------

    Route::middleware('auth:api')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);





        //----------------------------------Parcel----------------------------------------

        Route::resource('/parcel', ParcelController::class)
            ->only(['index', 'show', 'store', 'update', 'destroy']);

        //---------------------------------Authorization--------------------------------------------

        Route::resource('/authorization', AuthorizationController::class)
            ->only(['index', 'show', 'store', 'update', 'destroy']);

        Route::post('/authorization/use/{id}', [AuthorizationController::class, 'use'])
            ->name('authorization.use');

        //--------------------------------Rates----------------------------------------------------
        Route::resource('/rates', RatesController::class)
            ->only(['index', 'show', 'store', 'update', 'destroy']);

        //--------------------------------Users----------------------------------------------------

        Route::get('/users', UsersController::class)->name('users');

        //-------------------------------Notification---------------------------------------------

        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::post('/', [NotificationController::class, 'store']);
            Route::post('/bulk', [NotificationController::class, 'sendBulk']);
            Route::get('/stats', [NotificationController::class, 'getStats']);
            Route::get('/unread-count', [NotificationController::class, 'getUnreadCount']);
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
            Route::delete('/{id}', [NotificationController::class, 'delete']);
            Route::post('/test-broadcast', [NotificationController::class, 'testBroadcast']);
        });

        //-------------------------------Broadcasting---------------------------------------------

        Route::prefix('broadcasting')->group(function () {
            Route::post('/auth', [BroadcastController::class, 'authenticate']);
            Route::get('/channels', [BroadcastController::class, 'channels']);
            Route::get('/test', [BroadcastController::class, 'test']);
        });
    });
});
// ->middleware('throttle:6');
