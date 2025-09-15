<?php

namespace Database\Seeders;

use App\Models\UsagePolicies;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsagePoliciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policies = [
            [
                'policy_type' => 'general',
                'policy_name' => 'Terms of Service',
                'policy_description' => 'These are the terms and conditions of using our service...',
            ],
            [
                'policy_type' => 'privacy',
                'policy_name' => 'Privacy Policy',
                'policy_description' => 'We value your privacy and protect your personal data...',
            ],
            [
                'policy_type' => 'usage',
                'policy_name' => 'Acceptable Use Policy',
                'policy_description' => 'Rules and guidelines for acceptable usage of the platform...',
            ],
        ];

        foreach ($policies as $policy) {
            UsagePolicies::updateOrCreate(
                ['policy_name' => $policy['policy_name']], // شرط التحديث إذا الاسم موجود
                $policy
            );
        }
    }
}
