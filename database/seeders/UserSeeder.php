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
                'first_name' => 'user2',
                'last_name' => '',
                'email' => 'user2@gmail.com',
                'password' => Hash::make('user1234'),
                'address' => 'address',
                'phone' => '0998765421',
                'national_number' => 11345678902,
                'birthday' => '2000-02-01',
                'email_verified_at' => now(),
                'city_id' => 1,
            ],
            [
                'first_name' => 'user3',
                'last_name' => '',
                'email' => 'user3@gmail.com',
                'password' => Hash::make('user1234'),
                'address' => 'address',
                'phone' => '0998165431',
                'national_number' => 12341678902,
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
            [
                'first_name' => 'employee1',
                'last_name' => 'branch',
                'email' => 'employee1@gmail.com',
                'password' => Hash::make('employee'),
                'address' => 'address',
                'phone' => '0998765436',
                'national_number' => 12345678904,
                'birthday' => '1999-05-01',
                'email_verified_at' => now(),
                'city_id' => 1,
            ],
            [
                'first_name' => 'employee2',
                'last_name' => 'branch',
                'email' => 'employee2@gmail.com',
                'password' => Hash::make('employee'),
                'address' => 'address',
                'phone' => '0998765437',
                'national_number' => 12345678905,
                'birthday' => '1998-06-01',
                'email_verified_at' => now(),
                'city_id' => 1,
            ],
            [
                'first_name' => 'employee3',
                'last_name' => 'branch',
                'email' => 'employee3@gmail.com',
                'password' => Hash::make('employee'),
                'address' => 'address',
                'phone' => '0998765438',
                'national_number' => 12345678906,
                'birthday' => '1997-07-01',
                'email_verified_at' => now(),
                'city_id' => 1,
            ],
        ];
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
