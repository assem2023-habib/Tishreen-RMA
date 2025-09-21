<?php

namespace App\Filament\Forms\Components;

use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class PhoneNumber
{
    public static function make(string $name = 'phone', ?string $label = null): PhoneInput
    {
        return PhoneInput::make($name)
            ->label($label ?? 'Phone Number')
            ->defaultCountry('SY') // ← هنا بتحدد سوريا كدولة افتراضية
            ->autoPlaceholder('aggressive')
            ->helperText('Include country code, e.g. +9639XXXXXXX')
            ->rules(['required', 'regex:/^(\+?\d{6,15})$/'])
            ->validationMessages([
                'required' => 'Phone number is required',
                'regex' => 'Invalid phone number format',
            ]);
    }
}
