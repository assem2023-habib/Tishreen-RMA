<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. تعديل الجدول الوسيط notification_user
        Schema::table('notification_user', function (Blueprint $table) {
            // إضافة الحقول المطلوبة
            if (!Schema::hasColumn('notification_user', 'notifiable_type')) {
                $table->string('notifiable_type')->after('notification_id');
                $table->unsignedBigInteger('notifiable_id')->after('notifiable_type');
                $table->index(['notifiable_type', 'notifiable_id']);
            }

            if (!Schema::hasColumn('notification_user', 'data')) {
                $table->json('data')->nullable()->after('notifiable_id');
            }

            // حذف الحقول غير المطلوبة
            if (Schema::hasColumn('notification_user', 'is_read')) {
                $table->dropColumn('is_read');
            }

            // التأكد من وجود read_at (لأننا حذفنا is_read)
            if (!Schema::hasColumn('notification_user', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('data');
            }

            // حذف user_id القديم لأنه استبدل بـ notifiable
            if (Schema::hasColumn('notification_user', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        // 2. تنظيف جدول notifications من حقول الارتباط الفردية
        Schema::table('notifications', function (Blueprint $table) {
            $columns = ['notifiable_type', 'notifiable_id', 'data', 'read_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('notifications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_user', function (Blueprint $table) {
            $table->dropMorphs('notifiable');
            $table->dropColumn('data');
            $table->boolean('is_read')->default(false);
            $table->unsignedBigInteger('user_id')->nullable();
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->morphs('notifiable');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
        });
    }
};
