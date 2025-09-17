<?php

namespace App\Filament\Resources\ParcelResource\Pages;

use App\Enums\ParcelStatus;
use App\Filament\Resources\ParcelResource;
use App\Models\BranchRoute;
use Filament\Actions;
use Filament\Forms\Components\{Grid, Select, TextInput, Toggle};
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class EditParcel extends EditRecord
{
    protected static string $resource = ParcelResource::class;

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
    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                Select::make('route_id')
                    ->label('Branches Route')
                    ->options(function () {
                        return BranchRoute::select('id', 'from_branch_id', 'to_branch_id')
                            ->get()
                            ->mapWithKeys(function ($branchRoute) {
                                return [$branchRoute->id => $branchRoute->fromBranch->branch_name . " --> " . $branchRoute->toBranch->branch_name];
                            });
                    }),

                TextInput::make('reciver_name')
                    ->required(),

                TextInput::make('reciver_address')
                    ->required(),
                PhoneInput::make('reciver_phone')
                    ->label('Reciver Phone')
                    ->autoPlaceholder('aggressive')
                    ->helperText('Include country code, e.g. +9639XXXXXXX')
                    ->rules(['required', 'regex:/^(\+?\d{6,15})$/'])
                    ->validationMessages([
                        'required' => 'Phone number is required',
                        'regex' => 'Invalid phone number format',
                    ]),

                Select::make('parcel_status')
                    ->options(ParcelStatus::class)
                    ->enum(ParcelStatus::class),

                TextInput::make('weight')
                    ->numeric()
                    ->required(),

                TextInput::make('cost')
                    ->numeric()
                    ->readOnly(),

                Toggle::make('is_paid')
                    ->label('Paid'),
            ]),
        ]);
    }
}
