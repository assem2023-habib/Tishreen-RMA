<?php

namespace App\Filament\Resources;

use App\Enums\NotificationPriority;
use App\Filament\Resources\NotificationResource\Pages;
use App\Filament\Resources\NotificationResource\RelationManagers;
use App\Models\Notification;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationGroup = "Notifications";
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('message')
                    ->required()
                    ->maxLength(512),
                Select::make('notification_priority')
                    ->label('الأولوية')
                    ->options(NotificationPriority::class)
                    ->required(),
                Select::make('notification_type')
                    ->label('نوع الإشعار')
                    ->options([
                        'info' => 'معلومات',
                        'success' => 'نجاح',
                        'warning' => 'تحذير',
                        'danger' => 'خطر',
                        'reminder' => 'تذكير',
                        'update' => 'تحديث',
                        'announcement' => 'إعلان',
                    ])
                    ->required()
                    ->searchable(),
                Select::make('user_ids')
                    ->label('المستخدمون')
                    ->multiple()
                    ->relationship('users', 'name')
                    ->preload()
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->first_name . ' ' . $record->last_name),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->sortable()->searchable(),
                TextColumn::make('message')->limit(50),
                // Tables\Columns\IconColumn::make('notification_type')
                //     ->label('نوع الإشعار')
                //     ->icon('heroicon-o-information-circle')
                //     ->color('primary')
                //     ->getStateUsing(function ($state) {
                //         $iconMap = [
                //             'info' => 'heroicon-o-information-circle',
                //             'success' => 'heroicon-o-check-circle',
                //             'warning' => 'heroicon-o-exclamation-triangle',
                //             'danger' => 'heroicon-o-x-circle',
                //             'reminder' => 'heroicon-o-clock',
                //             'update' => 'heroicon-o-arrow-path',
                //             'announcement' => 'heroicon-o-megaphone',
                //         ];

                //         return $iconMap[$state] ?? 'heroicon-o-information-circle';
                //     })
                //     ->color(function ($state) {
                //         $colorMap = [
                //             'info' => 'primary',
                //             'success' => 'success',
                //             'warning' => 'warning',
                //             'danger' => 'danger',
                //             'reminder' => 'info',
                //             'update' => 'success',
                //             'announcement' => 'warning',
                //         ];

                //         return $colorMap[$state] ?? 'primary';
                //     }),
                // Tables\Columns\BadgeColumn::make('notification_type')
                //     ->label('نوع الإشعار')
                //     ->colors([
                //         'primary' => 'info',
                //         'success' => 'success',
                //         'warning' => 'warning',
                //         'danger' => 'danger',
                //     ])
                //     ->formatStateUsing(function ($state) {
                //         $types = [
                //             'info' => 'معلومات',
                //             'success' => 'نجاح',
                //             'warning' => 'تحذير',
                //             'danger' => 'خطر',
                //             'reminder' => 'تذكير',
                //             'update' => 'تحديث',
                //             'announcement' => 'إعلان',
                //         ];

                //         return $types[$state] ?? $state;
                //     }),
                TextColumn::make('notification_priority')
                    ->label('الأولوية')
                    ->colors([
                        'danger' => NotificationPriority::IMPORTANT->value,
                        'warning' => NotificationPriority::REMINDER->value,
                        'success' => 'default',
                    ])
                    ->badge(),
                TextColumn::make('user_names')
                    ->label('المستخدمون')
                    ->getStateUsing(fn($record) => $record->users->pluck('name')->join(', '))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                BooleanColumn::make('is_read_for_current_user')
                    ->label('مقروء؟')
                    ->getStateUsing(function ($record) {
                        $user = Auth::user();
                        $pivot = $record->users->firstWhere('id', $user->id)?->pivot;
                        return $pivot ? $pivot->is_read : false;
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('notification_type')
                    ->label('نوع الإشعار')
                    ->options([
                        'info' => 'معلومات',
                        'success' => 'نجاح',
                        'warning' => 'تحذير',
                        'danger' => 'خطر',
                        'reminder' => 'تذكير',
                        'update' => 'تحديث',
                        'announcement' => 'إعلان',
                    ]),
                Tables\Filters\SelectFilter::make('notification_priority')
                    ->label('الأولوية')
                    ->options(NotificationPriority::values()),
                Tables\Filters\Filter::make('unread_only')
                    ->label('غير مقروءة فقط')
                    ->query(fn(Builder $query) => $query->whereHas('users', function ($q) {
                        $q->where('user_id', Auth::user()->id())->where('is_read', false);
                    })),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('markAsRead')
                    ->label('تعليم كمقروء')
                    ->action(function ($record) {
                        $record->users()->updateExistingPivot(
                            Auth::user()->id,
                            ['is_read' => true, 'read_at' => now()]
                        );
                    })
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
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }
}
