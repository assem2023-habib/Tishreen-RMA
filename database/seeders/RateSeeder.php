<?php

namespace Database\Seeders;

use App\Enums\RatingForType;
use App\Models\Rate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rates = [
            // تقييم تطبيق عام
            [
                'user_id' => 2,
                'rateable_id' => null,
                'rating' => 5,
                'comment' => 'The application is very smooth and fast!',
                'rateable_type' => RatingForType::APPLICATION,
            ],

            // تقييم خدمة
            [
                'user_id' => 3,
                'rateable_id' => null,
                'rating' => 4,
                'comment' => 'Customer service was helpful and responsive.',
                'rateable_type' => RatingForType::SERVICE,
            ],

            // تقييم موظف
            [
                'user_id' => 2,
                'rateable_id' => 1, // يفترض أنه employee_id موجود
                'rating' => 2,
                'comment' => 'The employee was late to respond.',
                'rateable_type' => RatingForType::EMPLOYEE,
            ],

            // تقييم فرع
            [
                'user_id' => 4,
                'rateable_id' => 1, // يفترض أنه branch_id موجود
                'rating' => 3,
                'comment' => 'The branch was a bit crowded.',
                'rateable_type' => RatingForType::BRANCH,
            ],

            // تقييم طرد
            [
                'user_id' => 5,
                'rateable_id' => 1, // parcel_id
                'rating' => 4,
                'comment' => 'Parcel arrived safely and on time.',
                'rateable_type' => RatingForType::PARCEL,
            ],

            // تقييم توصيل
            [
                'user_id' => 3,
                'rateable_id' => null,
                'rating' => 5,
                'comment' => 'Delivery was super fast!',
                'rateable_type' => RatingForType::DELIVERY,
            ],

            // تقييم جلسة محادثة
            [
                'user_id' => 2,
                'rateable_id' => null,
                'rating' => 1,
                'comment' => 'Chat session disconnected unexpectedly.',
                'rateable_type' => RatingForType::CHATSESSION,
            ],
        ];
        foreach ($rates as $rate) {
            Rate::create($rate);
        }
    }
}
