<?php

namespace App\Observers;

use App\Models\User;

class UserObserve
{
    public function creating(User $user)
    {
        $user->user_name = $this->generateUserName(strtolower($user->first_name . '.' . $user->last_name));
    }
    private function generateUserName($userName)
    {
        $this->conatinsArabic($userName) ?
            $userName = $this->transliterateAndClean($userName)
            : $userName;
        $userName = str_replace(" ", "_", $userName);
        $userName = preg_replace('/[^a-z0-9\.]/', '', $userName);

        $maxAttempts = 5;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $randomNumber = rand(1, 999);
            $newUserName = "{$userName}_{$randomNumber}";

            if (!User::where('user_name', $newUserName)->exists()) {
                return $newUserName;
            }
        }
        return $userName . substr(time(), -4);
    }
    private function conatinsArabic($text)
    {
        return preg_match('/\p{Arabic}/u', $text);
    }
    /**
     * Transliterate Arabic/non-Latin characters and clean the string
     */
    private function transliterateAndClean(string $text): string
    {
        // خريطة تحويل الأحرف العربية إلى لاتينية
        $arabicToLatin = [
            'أ' => 'a',
            'إ' => 'a',
            'آ' => 'a',
            'ا' => 'a',
            'ب' => 'b',
            'ت' => 't',
            'ث' => 'th',
            'ج' => 'j',
            'ح' => 'h',
            'خ' => 'kh',
            'د' => 'd',
            'ذ' => 'th',
            'ر' => 'r',
            'ز' => 'z',
            'س' => 's',
            'ش' => 'sh',
            'ص' => 's',
            'ض' => 'd',
            'ط' => 't',
            'ظ' => 'z',
            'ع' => 'a',
            'غ' => 'gh',
            'ف' => 'f',
            'ق' => 'q',
            'ك' => 'k',
            'ل' => 'l',
            'م' => 'm',
            'ن' => 'n',
            'ه' => 'h',
            'و' => 'w',
            'ي' => 'y',
            'ى' => 'a',
            'ة' => 'h',
            'ء' => 'a'
        ];

        // تحويل الأحرف العربية
        $text = strtr($text, $arabicToLatin);

        // إزالة الأحرف الخاصة والرموز، الإبقاء على الأحرف والأرقام فقط
        $text = preg_replace('/[^a-zA-Z0-9]/', '', $text);

        return $text;
    }
}
