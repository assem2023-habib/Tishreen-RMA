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
        ]);
        return $user;
    }
}
