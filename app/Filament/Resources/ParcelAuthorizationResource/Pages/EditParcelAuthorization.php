<?php

namespace App\Filament\Resources\ParcelAuthorizationResource\Pages;

use App\Enums\AuthorizationStatus;
use App\Enums\SenderType;
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

                        if ($type === SenderType::AUTHENTICATED_USER->value) {
                            return User::pluck('user_name', 'id');
                        }

                        if ($type === SenderType::GUEST_USER->value) {
                            return GuestUser::pluck('full_name', 'id'); // أو أي حقل يمثل الاسم
                        }

                        return [];
                    })
                    ->searchable()
                    ->visible(fn(callable $get) => $get('authorized_user_type') === User::class),

                // مستلم مسجل
                Select::make('authorized_user_id')
                    ->label('Receiver')
                    ->relationship('authorizedUser', 'user_name')
                    ->searchable()
                    ->visible(fn(callable $get) => $get('authorized_user_type') === User::class),

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
