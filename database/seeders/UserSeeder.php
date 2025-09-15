<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'admin',
                'last_name' => '',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'address' => 'address',
                'phone' => '0998765432',
                'national_number' => 12345678901,
                'birthday' => '2000-02-01',
                'email_verified_at' => now(),
                'city_id' => 1,
            ],
            [
                'first_name' => 'user',
                'last_name' => '',
                'email' => 'user@gmail.com',
                'password' => Hash::make('user1234'),
                'address' => 'address',
                'phone' => '0998765431',
                'national_number' => 12345678902,
                'birthday' => '2000-02-01',
                'email_verified_at' => now(),
                'city_id' => 1,
            ],
            [
                'first_name' => 'test',
                'last_name' => '',
                'email' => 'test@gmail.com',
                'password' => Hash::make('user1234'),
                'address' => 'address',
                'phone' => '0998765435',
                'national_number' => 12345678903,
                'birthday' => '2000-02-04',
                'email_verified_at' => now(),
                'city_id' => 1,
            ],
        ];
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
