<?php

use App\Http\Controllers\Api\V1\Admin\AdminParcelController;
use App\Http\Controllers\Api\V1\Admin\AdminShipmentController;
use App\Http\Controllers\Api\V1\Admin\AdminTruckController;
use App\Http\Controllers\Api\V1\Admin\AdminAppointmentController;
use App\Http\Controllers\Api\V1\Appointment\AppointmentController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\TelegramOtpController;
use App\Http\Controllers\Api\V1\Authorization\AuthorizationController;
use App\Http\Controllers\Api\V1\Branche\BranchByCityController;
use App\Http\Controllers\Api\V1\Branche\BranchController;
use App\Http\Controllers\Api\V1\Branche\GetBranchById;
use App\Http\Controllers\Api\V1\BranchRoute\BranchRouteController;
use App\Http\Controllers\Api\V1\CountryAndCity\CountryController;
use App\Http\Controllers\Api\V1\Parcel\ParcelController;
use App\Http\Controllers\Api\V1\PricingPolicy\PricingPolicyController;
use App\Http\Controllers\Api\V1\Rates\RatesController;
use App\Http\Controllers\Api\V1\Statistics\StatisticsController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\Users\UsersController;
use Illuminate\Support\Facades\Route;

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

    //-------------------------------BranchRoute-----------------------------------------------
    Route::get('/routes', BranchRouteController::class);
    Route::get('/routes/{day}', [BranchRouteController::class, 'showRoutesByDay']);

    //----------------------------------Appointment-------------------------------------
    Route::get('/get-getCalender/{tracking_number}', [AppointmentController::class, 'getCalenderByParcelTrackingNumber']);
    Route::post('/book-appointment', [AppointmentController::class, 'bookAppointment']);
    Route::post('/cancel-appointment', [AppointmentController::class, 'cancelAppointment']);
    Route::post('/update-appointment', [AppointmentController::class, 'updateAppointment']);

    //----------------------------------Branches-------------------------------------
    Route::get('/branches', BranchController::class)->name('branches.index');
    Route::get('/branches/{cityId}', BranchByCityController::class)->name('branches.show');
    Route::get('/branches/{id}/detail', GetBranchById::class);

    //-----------------------------------OTP TELEGRAM----------------------------------
    Route::prefix('telegram')->group(function () {
        Route::post('otp/send', [TelegramOtpController::class, 'send']);
        Route::post('otp/verify', [TelegramOtpController::class, 'verify']);
        Route::post('webhook', [TelegramOtpController::class, 'handle']); // من اجل اعطاء معرف المحادثة ضمن بوت التلغرام
    });

    //----------------------------------PricingPolicy---------------------------------
    Route::get('/pricing-policy', PricingPolicyController::class)->name('pricingPolicy.index');

    //----------------------------------------------------------------------------------------------------------------------------
    Route::middleware('auth:api')->group(function () {

        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('update-profile', [AuthController::class, 'updateProfile']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);

        //----------------------------------Parcel----------------------------------------
        Route::get('/parcel/returned', [ParcelController::class, 'returnedParcels']);
        Route::get('/parcel/{tracking_number}/location', [ParcelController::class, 'getParcelLocation']);

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
        Route::get('/users/search', [UsersController::class, 'search'])->name('users.search');

        //--------------------------------Statistics-----------------------------------------------
        Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');

        //--------------------------------Notifications--------------------------------------------
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);

        //--------------------------------Chat Support---------------------------------------------
        Route::prefix('chat')->group(function () {
            // Conversations
            Route::get('/conversations', [\App\Http\Controllers\Api\V1\Chat\ConversationController::class, 'index']);
            Route::post('/conversations', [\App\Http\Controllers\Api\V1\Chat\ConversationController::class, 'store']);
            Route::get('/conversations/{id}', [\App\Http\Controllers\Api\V1\Chat\ConversationController::class, 'show']);
            Route::post('/conversations/{id}/close', [\App\Http\Controllers\Api\V1\Chat\ConversationController::class, 'close']);

            // Messages
            Route::get('/conversations/{id}/messages', [\App\Http\Controllers\Api\V1\Chat\MessageController::class, 'index']);
            Route::post('/conversations/{id}/messages', [\App\Http\Controllers\Api\V1\Chat\MessageController::class, 'store']);
        });

        //--------------------------------Admin/Employee Endpoints--------------------------------
        Route::prefix('admin')->middleware('employee')->group(function () {
            // Branch Info
            Route::get('/my-branch', [AdminParcelController::class, 'myBranch']);

            // Parcels Management
            Route::get('/parcels', [AdminParcelController::class, 'index']);
            Route::get('/parcels/{id}/history', [AdminParcelController::class, 'history']);
            Route::post('/parcels/{id}/confirm-reception', [AdminParcelController::class, 'confirmReception']);
            Route::post('/parcels/{id}/update-status', [AdminParcelController::class, 'updateStatus']);

            // Appointments Management
            Route::get('/appointments', [AdminAppointmentController::class, 'index']);
            Route::post('/appointments/{id}/status', [AdminAppointmentController::class, 'updateStatus']);

            // Shipments Management
            Route::get('/shipments', [AdminShipmentController::class, 'index']);
            Route::post('/shipments/{id}/depart', [AdminShipmentController::class, 'depart']);
            Route::post('/shipments/{id}/arrive', [AdminShipmentController::class, 'arrive']);

            // Truck Management
            Route::get('/trucks', [AdminTruckController::class, 'index']);
            Route::get('/trucks/{id}', [AdminTruckController::class, 'show']);
            Route::post('/trucks/{id}/toggle-status', [AdminTruckController::class, 'toggleStatus']);
        });
    });
});
