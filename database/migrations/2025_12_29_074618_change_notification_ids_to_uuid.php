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
        // Disable foreign key checks to allow truncation
        Schema::disableForeignKeyConstraints();
        DB::table('notification_user')->truncate();
        DB::table('notifications')->truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Drop foreign key
        Schema::table('notification_user', function (Blueprint $table) {
            $table->dropForeign(['notification_id']);
        });

        // 2. Change notifications.id to UUID string
        // Remove auto-increment and change type
        DB::statement('ALTER TABLE notifications MODIFY id VARCHAR(36) NOT NULL');

        // 3. Change notification_user.notification_id to string to match UUID
        Schema::table('notification_user', function (Blueprint $table) {
            $table->string('notification_id', 36)->change();
        });

        // 4. Restore foreign key
        Schema::table('notification_user', function (Blueprint $table) {
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_user', function (Blueprint $table) {
            $table->dropForeign(['notification_id']);
        });

        DB::statement('ALTER TABLE notifications MODIFY id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL');

        Schema::table('notification_user', function (Blueprint $table) {
            $table->unsignedBigInteger('notification_id')->change();
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
        });
    }
};
