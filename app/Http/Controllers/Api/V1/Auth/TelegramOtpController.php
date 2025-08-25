<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\SendTelegramOtpRequest;
use App\Http\Requests\Api\V1\Auth\VerifyTelegramOtpRequest;
use App\Models\TelegramOtp;
use App\Services\TelegramOtpService;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class TelegramOtpController extends Controller
{
    use ApiResponse;
    protected $otpService;
    public function __construct(TelegramOtpService $otpService)
    {
        $this->otpService = $otpService;
    }
    public function send(SendTelegramOtpRequest $request)
    {
        $validated = $request->validated();
        $success = $this->otpService->sendOtp($validated['chat_id']);
        // return $this->successResponse(
        //     $success,
        //     $success ? "otp Sent!" : "failed to send OTP",
        // );
        if (!$success)
            return $this->errorResponse(
                "failed to send otp",
                HttpStatus::BAD_REQUEST->value
            );
        return $this->successResponse(
            [],
            "otp Send!",
            HttpStatus::OK->value,
        );
    }
    public function verify(VerifyTelegramOtpRequest $requset)
    {
        $validated = $requset->validated();
        $verified = $this->otpService->verifyOtp($validated['chat_id'], $validated['otp']);
        if (!$verified)
            return $this->errorResponse(
                "invalid or Expire Otp",
                HttpStatus::UNPROCESSABLE_ENTITY->value,
            );
        return $this->successResponse(
            [],
            "Otp verfied!.",

        );
    }
}
