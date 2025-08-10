<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FrequentlyAskedQuestions;

class FrequentlyAskedQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'ما هي سياسة الإرجاع؟',
                'answer' => 'يمكنك إرجاع المنتجات خلال 14 يومًا من تاريخ الشراء مع الفاتورة.',
                'category_type' => 'return_policy',
                'is_show' => 1,
            ],
            [
                'question' => 'كيف يمكنني تتبع طلبي؟',
                'answer' => 'يمكنك تتبع الطلب من خلال صفحة حسابي باستخدام رقم الطلب.',
                'category_type' => 'order_tracking',
                'is_show' => 1,
            ],
            [
                'question' => 'ما هي طرق الدفع المتاحة؟',
                'answer' => 'نقبل الدفع نقدًا، عن طريق بطاقة الائتمان، والدفع عند الاستلام.',
                'category_type' => 'payment_methods',
                'is_show' => 1,
            ],
        ];

        foreach ($faqs as $faq) {
            FrequentlyAskedQuestions::updateOrCreate(
                ['question' => $faq['question']],
                $faq
            );
        }
    }
}
