<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\ActiveToggle;
use App\Filament\Helpers\TableActions;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Tables\Actions\ToggleEmployeeRole;
use App\Filament\Tables\Columns\ActiveToggleColumn;
use App\Filament\Tables\Columns\Timestamps;
use App\Models\{Branch, Employee, User};
use Carbon\Carbon;
use Filament\Forms\Components\{Grid, DatePicker, Select};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

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
                        ->options(
                            function () {
                                return User::pluck(DB::raw("CONCAT(first_name, ' ', last_name)"), 'id');
                            }
                        )
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
                        ActiveToggle::makeRtl('is_active', '...? is Working'),

                    ]
                )
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
                ActiveToggleColumn::make('is_active'),
                ...Timestamps::make()
            ])
            ->filters([
                //
            ])
            ->actions([
                ToggleEmployeeRole::make(),
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
