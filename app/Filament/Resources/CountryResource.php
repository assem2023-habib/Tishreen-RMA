<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Filament\Resources\CountryResource\RelationManagers\CityRelationManager;
use App\Models\Country;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;


class CountryResource extends Resource
{
    protected static ?string $model = Country::class;
    protected static ?string $navigationIcon = 'heroicon-o-globe-americas';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('ar_name')
                    ->label('Country arabic name')
                    ->helperText('Only Arabic characters')
                    ->validationMessages([
                        'regex' => 'Only Arabic characters',
                        'required' => 'This field is requied',
                    ]),
                TextInput::make('en_name')
                    ->label('Country english name')
                    ->helperText('Only English characters')
                    ->rules(['required', 'regex:/^[A-Za-z\s]+$/'])
                    ->validationMessages([
                        'regex' => 'Only English characters',
                        'required' => 'This field is requied',
                    ]),
                TextInput::make('code')
                    ->maxLength(5),
                TextInput::make('domain_code')
                    ->label('Domain code')
                    ->maxLength(10),
                FileUpload::make('image')
                    ->disk('public')
                    ->image()
                    ->imageEditor()
                    ->imageEditorViewportWidth('1920')
                    ->imageEditorViewportHeight('1080')
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetWidth(40)
                    ->imageResizeTargetHeight(40)
                    ->helperText('Please make user the image size is 40*40')
                    ->directory('flags')
                    ->label('Image'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Country id'),
                TextColumn::make('code')
                    ->label('Code'),
                TextColumn::make('domain_code')
                    ->label('Domain code'),
                TextColumn::make('ar_name')
                    ->label('Country arabic name'),
                TextColumn::make('en_name')
                    ->label('Country english name'),
                ImageColumn::make('image')
                    ->disk('public')->label('image'),
            ])
            ->defaultSort('ar_name')
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()->label('Edit and City managment'),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            CityRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountry::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
