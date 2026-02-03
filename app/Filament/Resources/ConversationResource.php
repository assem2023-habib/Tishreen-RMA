<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConversationResource\Pages;
use App\Models\Conversation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ConversationResource extends Resource
{
    protected static ?string $model = Conversation::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'المحادثات';
    protected static ?string $modelLabel = 'محادثة';
    protected static ?string $pluralModelLabel = 'المحادثات';
    protected static ?string $navigationGroup = 'الدعم الفني';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')
             ->whereNull('employee_id')
             ->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('subject')
                    ->label('الموضوع')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'في الانتظار',
                        'open' => 'جارية',
                        'closed' => 'مغلقة',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('customer.user_name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('الموضوع')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('related_type')
                    ->label('الارتباط')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'App\\Models\\Parcel' => 'طرد',
                            'App\\Models\\Branch' => 'فرع',
                            'App\\Models\\Appointment' => 'موعد',
                            default => 'عام',
                        };
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'open',
                        'danger' => 'closed',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'في الانتظار',
                        'open' => 'جارية',
                        'closed' => 'مغلقة',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('employee.user.user_name') // Assuming Employee belongsTo User
                    ->label('الموظف المسؤول')
                    ->placeholder('غير معين'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'في الانتظار',
                        'open' => 'جارية',
                        'closed' => 'مغلقة',
                    ]),
                Tables\Filters\Filter::make('unassigned')
                    ->label('غير معينة (انتظار)')
                    ->query(fn (Builder $query) => $query->whereNull('employee_id')->where('status', 'pending'))
                    ->default(), // Default filter
                Tables\Filters\Filter::make('my_conversations')
                    ->label('محادثاتي')
                    ->query(fn (Builder $query) => $query->whereHas('employee', function($q) {
                        $q->where('user_id', Auth::id());
                    })),
            ])
            ->actions([
                Tables\Actions\Action::make('take')
                    ->label('استلام')
                    ->icon('heroicon-o-hand-raised')
                    ->color('success')
                    ->visible(fn (Conversation $record) => $record->employee_id === null && $record->status !== 'closed')
                    ->action(function (Conversation $record) {
                        $employee = \App\Models\Employee::where('user_id', Auth::id())->first();
                        if ($employee) {
                            $record->assignToEmployee($employee);
                            
                            // Broadcast update
                            broadcast(new \App\Events\ConversationUpdatedEvent($record, 'assigned'));
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('خطأ')
                                ->body('حسابك الحالي غير مرتبط بملف موظف.')
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\ViewAction::make()->label('فتح المحادثة'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->poll('10s'); // Poll for new tickets
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
            'index' => Pages\ListConversations::route('/'),
            // 'create' => Pages\CreateConversation::route('/create'), // No internal creation for now
            'view' => Pages\ViewConversation::route('/{record}'),
        ];
    }
    
    // لإخفاء زر الإنشاء إذا أردنا أن ينشئ العملاء فقط المحادثات
    public static function canCreate(): bool
    {
        return false; 
    }
}
