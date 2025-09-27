<?php

namespace App\Filament\Resources;

use \Log;
use App\Filament\Forms\Components\PhoneNumber;
use App\Filament\Helpers\TableActions;
use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use App\Models\City;
use App\Services\GeocodingService;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\{Grid, TextInput, Select};
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use function PHPUnit\Framework\isEmpty;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';
    protected static ?string $navigationGroup = 'Geographical Data';
    protected static ?int $navigationSort = 3;
    protected static bool $shouldRegisterNavigation = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)->schema([
                    /** @var \Dotswan\MapPicker\Fields\Map $map */
                    Map::make('map')
                        ->defaultLocation(latitude: 33.5138, longitude: 36.2765) // the default position is damscus
                        ->draggable(true)
                        ->clickable(true)
                        ->zoom(9)
                        ->showMarker(true)
                        ->markerColor("#3b82f6")
                        ->markerHtml(self::getMapStyle())
                        ->extraStyles([
                            'min-height: 60vh',
                            'border-radius: 50px',
                            'z-index: 1'
                        ])
                        ->showFullscreenControl(true)
                        ->showZoomControl(true)
                        ->liveLocation(true)
                        // @intelephense-ignore-next-line
                        ->afterStateUpdated(
                            callback: function (Set $set, ?array $state): void {
                                $lat = data_get($state, 'lat');
                                $lng = data_get($state, 'lng');
                                if (! $lat || ! $lng) {
                                    return;
                                }
                                $set('latitude', $lat);
                                $set('longitude', $lng);
                                $response = GeocodingService::reverseGeocode($lat, $lng);
                                $set('address', $response['display_name'] ?? 'Unknown');
                            }
                        ),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('latitude')
                        ->readonly(),
                    TextInput::make('longitude')
                        ->readonly(),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('branch_name')
                        ->label('Branch Name')
                        ->rules(
                            [
                                'min:2',
                                'max:64',
                            ]
                        ),
                    TextInput::make('address')
                        ->label('Address')
                        ->rules(
                            [
                                'min:2',
                                'max:128',
                            ]
                        ),
                    PhoneNumber::make('phone', 'Phone Number', false),
                    Select::make('city_id')
                        ->label('city')
                        ->options(City::all()->pluck('en_name', 'id'))
                        ->searchable()
                        ->required(),
                    TextInput::make('email')
                        ->label('Email')
                        ->rules(
                            [
                                'email',
                                'min:4',
                                'max:255',
                            ]
                        ),

                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('branch_name')
                    ->label('Branch Name')
                    ->searchable(),
                TextColumn::make('city.ar_name')
                    ->label('City Name')
                    ->searchable(),
                TextColumn::make('phone'),
            ])
            ->header(
                function () {
                    $branches = Branch::all();
                    $branchesForMap = $branches->map(function ($branch) {
                        return [
                            'lat' => $branch->latitude,
                            'lng' => $branch->longitude,
                            'name' => $branch->branch_name,
                            'email' => $branch->email,
                        ];
                    })->toArray();

                    return view('filament.branches.map-view', [
                        'branchesForMap' => $branchesForMap,
                        'centerLatitude' => 33.5138,
                        'centerLongitude' => 36.2765,
                        'zoomLevel' => 18,
                    ]);
                }
            )
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    private static function getMapStyle(): HtmlString
    {
        return
            new HtmlString('
                            <style>
                                #branch-pointer {
                                position:relative;
                                background-color: #3b82f6;
                                width:fit-content;
                                color: white;
                                padding: 8px 12px;
                                border-radius: 20px;
                                font-weight: bold;
                                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                                display: flex;
                                align-items: center;
                                gap: 8px;
                                transition: all 0.3s ease;
                                transform:scale(1);
                                }
                                #branch-pointer:hover{
                                    transform:scale(1.05);
                                }
                            </style>
                            <div id="branch-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            الفرع
                        </div>');
    }
    private static function getCordinateAndAddress()
    {
        return;
    }
}
