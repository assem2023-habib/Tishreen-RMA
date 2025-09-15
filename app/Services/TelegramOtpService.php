<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\TelegramOtp;
use Carbon\Carbon;

class TelegramOtpService
{
    protected  $botToken;
    public function __construct()
    {
        $this->botToken = config('services.telegram.botToken');
    }
    public function generateOtp($chatId)
    {
        $otp = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinute(10);
        TelegramOtp::UpdateOrCreate(
            [
                'chat_id' => $chatId
            ],
            ['otp' => $otp, 'expires_at' => $expiresAt],
        );
        return  $otp;
    }
    public function sendOtp($chatId)
    {
        $otp = $this->generateOtp($chatId);
        $response = Http::get("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => "your verify token is : {$otp} \n he will expire after 5 minutes from now."
        ]);
        return $response->successful();
    }
    public function verifyOtp($chatId, $otp)
    {
        $record = TelegramOtp::where('chat_id', $chatId)
            ->where('otp', $otp)
            ->where('expires_at', '>=', now())
            ->first();
        if ($record) {
            $record->delete();
            return true;
        }
        return false;
    }
}
