<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{

    public function createUser($data)
    {

        // $userName = $this->generateUniqueUserName($data['first_name'], $data['last_name']);

        $user = User::create([
            'first_name'      => $data['first_name'],
            'last_name'       => $data['last_name'],
            'email'           => $data['email'],
            'password'        => Hash::make($data['password']),
            'phone'           => $data['phone'],
            'birthday'        => $data['birthday'],
            'city_id'         => $data['city_id'],
            'national_number' => $data['national_number'],
            // 'user_name'       =>  $userName,
        ]);
        return $user;
    }

    // protected function generateUniqueUserName($firstName, $lastName)
    // {
    //     $base = strtolower($firstName . '.' . $lastName);
    //     $base = preg_replace('/[^a-z0-9\.]/', '', $base);
    //     $userName = $base;
    //     $counter = 1;

    //     while (User::where('user_name', $userName)->exists()) {
    //         $userName = $base . $counter;
    //         $counter++;
    //     }

    //     return $userName;
    // }
}
