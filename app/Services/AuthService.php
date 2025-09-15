<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;

class AuthService
{
    public function login(array $credentials): array|false
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return false;
        }

        $token = $user->createToken('API Token')->accessToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function checkIfEmailVerifited($email): bool
    {

        $user = User::where('email', $email)->first();

        return $user->email_verified_at ? true : false;
    }

    public function createOtp(string $table, $email)
    {
        $otp = rand(100000, 999999);

        DB::table($table)->updateOrInsert(
            ['email' => $email],
            [
                'otp_code'   => $otp,
                'used'       => false,
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return $otp;
    }

    public function verifiyOtp($table, $email, $otp_code)
    {
        $record = DB::table($table)
            ->where('email', $email)
            ->where('otp_code', $otp_code)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();
        return $record;
    }
}
