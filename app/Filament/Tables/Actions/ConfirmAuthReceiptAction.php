<?php

namespace App\Filament\Tables\Actions;

use App\Enums\AuthorizationStatus;
use App\Enums\ParcelStatus;
use App\Models\ParcelAuthorization;
use App\Models\User;
use Filament\Tables\Actions\Action;
use App\Support\SharedNotification as Notification;
use Filament\Notifications\Actions\Action as NotificationAction;
use Illuminate\Support\Facades\DB;

class ConfirmAuthReceiptAction
{
    public static function make(): Action
    {
        return Action::make('confirmReceipt')
            ->label('تأكيد الاستلام')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('تأكيد تسليم الطرد عبر التخويل')
            ->modalDescription('هل أنت متأكد من رغبتك في وضع علامة "تم التسليم" لهذا الطرد للشخص المخول؟')
            ->visible(
                fn(ParcelAuthorization $record): bool =>
                $record->authorized_status !== AuthorizationStatus::USED->value &&
                    $record->authorized_status !== AuthorizationStatus::CANCELLED->value &&
                    $record->authorized_status !== AuthorizationStatus::EXPIRED->value
            )
            ->action(function (ParcelAuthorization $record): void {
                DB::transaction(function () use ($record) {
                    // 1. Update Authorization Status to USED
                    $record->update([
                        'authorized_status' => AuthorizationStatus::USED->value,
                        'used_at' => now(),
                    ]);

                    // 2. Update Linked Parcel Status to DELIVERED
                    if ($record->parcel) {
                        $record->parcel->update([
                            'parcel_status' => ParcelStatus::DELIVERED,
                        ]);
                    }
                });

                // 3. Send Notifications to Dashboard
                $parcel = $record->parcel;
                $sender = $parcel->sender;
                $originalReceiver = $record->user; // The one who granted the auth
                $authorizedProxy = $record->authorizedUser; // The one who received it

                $trackingNumber = $parcel->tracking_number ?? '---';

                // Helper function to get name from User or GuestUser
                $getName = fn($model) => $model ? ($model->user_name ?? ($model->first_name . ' ' . $model->last_name)) : '---';

                $proxyName = $getName($authorizedProxy);
                $receiverName = $getName($originalReceiver);

                // Notification to Sender
                if ($sender instanceof User) {
                    Notification::make()
                        ->title('تأكيد استلام طرد')
                        ->body("تم تسليم طردك ذو الرقم المرجعي ($trackingNumber) إلى الشخص المخول ($proxyName) بنجاح.")
                        ->success()
                        ->icon('heroicon-o-check-circle')
                        ->actions([
                            NotificationAction::make('view')
                                ->label('عرض الطرد')
                                ->url(fn() => "/admin/parcels/{$parcel->id}")
                        ])
                        ->sendToDatabase($sender)
                        ->broadcast($sender);
                }

                // Notification to Original Receiver (The one who granted the auth)
                if ($originalReceiver instanceof User) {
                    Notification::make()
                        ->title('تم تسليم الطرد للمخول')
                        ->body("تم تسليم الطرد الذي قمت بتخويل ($proxyName) لاستلامه بنجاح.")
                        ->success()
                        ->icon('heroicon-o-user-check')
                        ->sendToDatabase($originalReceiver)
                        ->broadcast($originalReceiver);
                }

                // Notification to Authorized Person (Proxy) - if registered
                if ($authorizedProxy instanceof User) {
                    Notification::make()
                        ->title('استلام طرد بنجاح')
                        ->body("لقد قمت باستلام الطرد ذو الرقم المرجعي ($trackingNumber) بنجاح نيابة عن ($receiverName).")
                        ->success()
                        ->icon('heroicon-o-shopping-bag')
                        ->sendToDatabase($authorizedProxy)
                        ->broadcast($authorizedProxy);
                }

                Notification::make()
                    ->title('تم تأكيد الاستلام')
                    ->body('تم وضع علامة "تم التسليم" على الطرد وتم تحديث حالة التخويل إلى "مستخدم". تم إرسال الإشعارات إلى لوحة تحكم المستخدمين المعنيين.')
                    ->success()
                    ->send();
            });
    }
}
