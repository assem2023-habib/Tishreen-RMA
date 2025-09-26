<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;

class NationalNumber
{
    public static function make(string $name = 'national_number', ?string $label = null)
    {
        return TextInput::make($name)
            ->label($label ?? 'National Number')
            ->helperText('Exactly 11 digits')
            ->unique(ignoreRecord: true)
            ->rules([
                'required',
                'digits:11',
                'regex:/^[0-9]+$/',
            ])
            ->validationMessages([
                'required' => 'National number is required',
                'digits' => 'Must be exactly 11 digits',
                'regex' => 'Only digits are allowed',
                'unique' => 'This national number is already used',
            ]);
    }
}
