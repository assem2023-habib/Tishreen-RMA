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
        Schema::table('notifications', function (Blueprint $table) {
            // Check if standard Laravel columns exist, if not add them
            if (!Schema::hasColumn('notifications', 'type')) {
                $table->string('type')->after('id')->nullable();
            }
            if (!Schema::hasColumn('notifications', 'notifiable_type')) {
                $table->morphs('notifiable');
            }
            if (!Schema::hasColumn('notifications', 'data')) {
                $table->text('data')->nullable();
            }
            if (!Schema::hasColumn('notifications', 'read_at')) {
                $table->timestamp('read_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropMorphs('notifiable');
            $table->dropColumn(['type', 'data', 'read_at']);
        });
    }
};
