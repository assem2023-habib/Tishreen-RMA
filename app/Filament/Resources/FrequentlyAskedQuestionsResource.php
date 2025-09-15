<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FrequentlyAskedQuestionsResource\Pages;
use App\Models\FrequentlyAskedQuestions;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class FrequentlyAskedQuestionsResource extends Resource
{
    protected static ?string $model = FrequentlyAskedQuestions::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = "Support & Information";

    protected static ?int $navigationSort = 2;
    protected static bool $shouldRegisterNavigation = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('question')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('answer')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('category_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('is_show')
                    ->required()
                    ->numeric()
                    ->default(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category_type')
                    ->searchable(),
                ToggleColumn::make('is_show')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-no-symbol')
                    ->onColor('success')
                    ->offColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('edite FAQ'),
                    Tables\Actions\ViewAction::make()
                        ->label('Show FAQ'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Delete FAQ'),
                ])
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
