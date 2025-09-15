<?php

namespace App\Filament\Resources\RateResource\Pages;

use App\Filament\Resources\RateResource;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Rate;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Card;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ViewRate extends ViewRecord
{
    protected static string $resource = RateResource::class;

    public function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
            ->schema([
                Section::make('Rating Details')
                    ->schema([
                        Card::make()
                            ->schema([
                                TextEntry::make('rating')
                                    ->label('Rating')
                                    ->formatStateUsing(fn($state) => str_repeat('⭐', (int) $state))
                                    ->color('warning')
                                    ->size('lg'),

                                TextEntry::make('comment')
                                    ->label('Comment')
                                    ->placeholder('No comment')
                                    ->size('md')
                                    ->columnSpanFull(),

                                TextEntry::make('user.user_name')
                                    ->label('Rated By')
                                    ->badge()
                                    ->color('success'),

                                TextEntry::make('rateable_type')
                                    ->label('Type')
                                    ->formatStateUsing(fn($state) => ucfirst($state))
                                    ->badge()
                                    ->color(fn($state) => match ($state) {
                                        'employee' => 'info',
                                        'branch'   => 'primary',
                                        default    => 'gray',
                                    }),

                                TextEntry::make('relatedDetails')
                                    ->label('Related Details')
                                    ->formatStateUsing(function ($record) {
                                        return match ($record->rateable_type) {
                                            'employee' => $this->getEmployeeDetails($record->rateable_id),
                                            'branch'   => $this->getBranchDetails($record->rateable_id),
                                            default    => 'N/A',
                                        };
                                    })
                                    ->color('primary')
                                    ->weight('bold'),

                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime('Y-m-d H:i')
                                    ->color('gray'),
                            ])
                            ->columns(2)
                    ]),
            ]);
    }

    private function getEmployeeDetails(int $id): string
    {
        $employee = Employee::with('user')->find($id);
        return $employee
            ? $employee->user->first_name . ' ' . $employee->user->last_name
            : 'Employee not found';
    }

    private function getBranchDetails(int $id): string
    {
        $branch = Branch::find($id);
        return $branch
            ? $branch->name
            : 'Branch not found';
    }
}
