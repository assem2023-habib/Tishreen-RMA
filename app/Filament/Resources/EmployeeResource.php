<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\{Branch, Employee, User};
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\{Grid, DatePicker, Select, Toggle};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = "Employees";
    protected static ?int $navigationSort = 1;

    protected static bool $shouldRegisterNavigation = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Select::make('user_id')
                        ->label('User')
                        ->placeholder('Please select the user')
                        ->options(function () {
                            return User::select('id', 'first_name', 'last_name')
                                ->get()
                                ->mapWithKeys(function ($user) {
                                    return  [$user->id => $user->first_name . " " . $user->last_name];
                                });
                        })
                        ->searchable()
                        ->preload(),
                    Select::make('branch_id')
                        ->label('Branch')
                        ->placeholder('Please select the Bracnh')
                        ->options(
                            function () {
                                return Branch::select('id', 'branch_name')
                                    ->get()
                                    ->mapWithKeys(
                                        function ($branch) {
                                            return [$branch->id => $branch->branch_name];
                                        }
                                    );
                            }
                        )
                        ->searchable()
                        ->preload(),
                ]),
                Grid::make(2)->schema(
                    [
                        DatePicker::make('beging_date')
                            ->required()
                            ->default(now()),
                        DatePicker::make('end_date'),
                    ]
                ),
                Grid::make(1)->schema(
                    [
                        Toggle::make('is_active')
                            ->label('...? is Working')
                            ->onIcon('heroicon-o-check-circle')
                            ->offIcon('heroicon-o-no-symbol')
                            ->onColor('success')
                            ->offColor('danger')
                            ->extraAttributes(['class' => 'ml-auto']),
                    ],
                )
                    // ->extraAttributes(['class' => 'h-full flex flex-col justify-end']),
                    ->extraAttributes(['class' => 'justify-end-safe', 'dir' => 'rtl']),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('user.user_name')
                    ->label('User Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('branch.branch_name')
                    ->label("Branch Name")
                    ->sortable(),
                TextColumn::make('beging_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->sortable()
                    ->formatStateUsing(
                        callback: function (?string $state) {
                            if (empty($state) || $state === '0000-00-00' || $state === 'NULL') {
                                return 'not Selected';
                            }
                            return Carbon::parse($state)->translatedFormat('Y-m-d');
                        }
                    ),
                ToggleColumn::make('is_active')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-no-symbol')
                    ->onColor('success')
                    ->offColor('danger'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
