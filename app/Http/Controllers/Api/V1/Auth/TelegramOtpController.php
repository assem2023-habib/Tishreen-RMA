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
use Illuminate\Support\Facades\Http;

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
    public function handle(Request $request)
    {
        $update = $request->all();
        if (!isset($update['message'])) {
            return $this->errorResponse(
                'no content',
                HttpStatus::NOT_CONTENT->value,
            );
        }
        $chatId = $update['message']['chat']['id'];
        $text = $update['message']['text'] ?? '';
        if ($text === '/chatid') {
            $this->sendMessage($chatId, "Your Chat ID is : " . $chatId);
        } elseif ($text === '/start') {
            $this->sendMessage($chatId, 'Welcom! Use /chatid to get your Chat ID.');
        }

        return $this->errorResponse(
            'no content',
            HttpStatus::NOT_CONTENT->value,
        );
    }
    private function sendMessage($chatId, $text)
    {
        $token = config('services.telegram.botToken');
        Http::get("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }
}
