<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;

class NationalNumber
{
    public static function make(string $name = 'national_number', ?string $label = null)
    {
        return TextInput::make($name)
            ->label($label ?? "National Number")
            ->maxLength(11)
            ->minLength(11)
            ->validationMessages(
                [
                    'required' => 'this field was required',
                ]
            );
    }
}
