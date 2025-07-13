<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use League\Csv\Serializer\CastToArray;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $base = Str::before($data['email'], '@');
        $username = $base;
        $count = 1;

        while (User::where('user_name', $username)->exists()) {
            $username = $base . $count++;
        }

        $data['user_name'] = $username;

        return $data;
    }
}
