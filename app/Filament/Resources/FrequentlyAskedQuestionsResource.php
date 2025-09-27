<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\ActiveToggle;
use App\Filament\Helpers\TableActions;
use App\Filament\Resources\FrequentlyAskedQuestionsResource\Pages;
use App\Filament\Tables\Columns\ActiveToggleColumn;
use App\Filament\Tables\Columns\Timestamps;
use App\Models\FrequentlyAskedQuestions;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class FrequentlyAskedQuestionsResource extends Resource
{
    protected static ?string $model = FrequentlyAskedQuestions::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = "Support & Information";

    protected static ?int $navigationSort = 3;
    protected static bool $shouldRegisterNavigation = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('question')
                    ->required()
                    ->maxLength(255),
                Textarea::make('answer')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('category_type')
                    ->required()
                    ->maxLength(255),
                ActiveToggle::make('is_show', 'Is Show ?')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question')
                    ->searchable(),
                TextColumn::make('category_type')
                    ->searchable(),
                ActiveToggleColumn::make('is_show'),
                ...Timestamps::make(),
            ])
            ->filters([
                //
            ])
            ->actions([
                TableActions::default(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFrequentlyAskedQuestions::route('/'),
            'create' => Pages\CreateFrequentlyAskedQuestions::route('/create'),
            'edit' => Pages\EditFrequentlyAskedQuestions::route('/{record}/edit'),
        ];
    }
}
