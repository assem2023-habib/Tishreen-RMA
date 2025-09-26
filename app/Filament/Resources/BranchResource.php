<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\PhoneNumber;
use \Log;
use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use App\Models\City;
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
                Grid::make(2)->schema([
                    Map::make('map')
                        ->defaultLocation(latitude: 33.5138, longitude: 36.2765) // the default position is damscus
                        ->draggable(true)
                        ->clickable(true)
                        ->zoom(9)
                        ->showMarker(true)
                        ->markerColor("#3b82f6")
                        ->markerHtml('
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
                        </div>')
                        ->columnSpanFull()
                        ->extraStyles([
                            'min-height: 60vh',
                            'border-radius: 50px',
                            'z-index: 1'
                        ])
                        ->showFullscreenControl(true)
                        ->showZoomControl(true)
                        ->liveLocation(true)
                        ->afterStateUpdated(
                            function (Set $set, ?array $state): void {
                                if (is_array($state) && isset($state['lat']) && isset($state['lng'])) {
                                    $lat = $state['lat'];
                                    $lng = $state['lng'];
                                }
                                // إذا كان الحقل يُرجع كائنًا (object)
                                elseif (is_object($state) && property_exists($state, 'lat') && property_exists($state, 'lng')) {
                                    $lat = $state->lat;
                                    $lng = $state->lng;
                                }
                                $set('latitude', $lat);
                                $set('longitude', $lng);
                                try {
                                    $response = Http::withHeaders([
                                        'User-Agent' => 'RMA (099assemhb@gmail.com)'
                                    ])->get("https://nominatim.openstreetmap.org/reverse", [
                                        'format' => 'jsonv2',
                                        'lat' => $state['lat'],
                                        'lon' => $state['lng'],
                                        'zoom' => 18, // مستوى التكبير للحصول على تفاصيل أدق
                                        'addressdetails' => 1 // لطلب تفاصيل العنوان
                                    ]);
                                    $city = $response['display_name'];
                                    $set('address', $city); // قم بتعيين اسم المدينة لحقل 'city'
                                } catch (\Exception $e) {


                                    $set('city', 'Error');
                                }
                            }
                        ),


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
                    // TextInput::make('phone')
                    //     ->label('Phone Number')
                    //     ->rules(
                    //         [
                    //             'min:2',
                    //             'max:20'
                    //         ]
                    //     ),
                    PhoneNumber::make('phone', 'Phone Number'),
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
