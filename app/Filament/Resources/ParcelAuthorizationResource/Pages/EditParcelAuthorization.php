<?php

namespace App\Filament\Resources\ParcelAuthorizationResource\Pages;

use App\Enums\AuthorizationStatus;
use App\Filament\Resources\ParcelAuthorizationResource;
use App\Models\{GuestUser, User};
use Filament\Actions;
use Filament\Forms\Components\{DateTimePicker, Select, TextInput};
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditParcelAuthorization extends EditRecord
{
    protected static string $resource = ParcelAuthorizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    public  function form(Form $form): Form
    {
        return $form
            ->schema([
                // المستخدم الأساسي
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'user_name')
                    ->searchable()
                    ->required(),

                // نوع المستلم
                Select::make('authorized_user_id')
                    ->label('Receiver')
                    ->options(function (callable $get) {
                        $type = $get('authorized_user_type');

                        if ($type === User::class) {
                            return User::pluck('user_name', 'id');
                        }

                        if ($type === GuestUser::class) {
                            return GuestUser::pluck('full_name', 'id'); // أو أي حقل يمثل الاسم
                        }

                        return [];
                    })
                    ->searchable()
                    ->visible(fn(callable $get) => $get('authorized_user_type') === User::class),
                // Select::make('authorized_user_type')
                //     ->label('Receiver Type')
                //     ->options([
                //         User::class => 'Authenticated User',
                //         GuestUser::class => 'Guest User',
                //     ])
                //     ->reactive()
                //     ->required(),

                // مستلم مسجل
                Select::make('authorized_user_id')
                    ->label('Receiver')
                    ->relationship('authorizedUser', 'user_name')
                    ->searchable()
                    ->visible(fn(callable $get) => $get('authorized_user_type') === User::class),

                // مستلم زائر
                // TextInput::make('guest_first_name')
                //     ->label('First Name')
                //     ->visible(fn(callable $get) => $get('authorized_user_type') === GuestUser::class),

                // TextInput::make('guest_last_name')
                //     ->label('Last Name')
                //     ->visible(fn(callable $get) => $get('authorized_user_type') === GuestUser::class),

                // TextInput::make('guest_phone')
                //     ->label('Phone')
                //     ->visible(fn(callable $get) => $get('authorized_user_type') === GuestUser::class),

                // Select::make('guest_city_id')
                //     ->label('City')
                //     ->relationship('city', 'en_name')
                //     ->searchable()
                //     ->visible(fn(callable $get) => $get('authorized_user_type') === GuestUser::class),

                // الطرد
                Select::make('parcel_id')
                    ->label('Parcel')
                    ->relationship('parcel', 'reciver_name')
                    ->searchable()
                    ->required(),

                // بيانات التفويض
                TextInput::make('authorized_code')
                    ->disabled(),

                Select::make('authorized_status')
                    ->label('Authorized Status')
                    ->options(AuthorizationStatus::class)
                    ->required(),

                DateTimePicker::make('generated_at')->required(),
                DateTimePicker::make('expired_at'),
                DateTimePicker::make('used_at'),

                TextInput::make('cancellation_reason')
                    ->maxLength(255),
            ]);
    }
}
