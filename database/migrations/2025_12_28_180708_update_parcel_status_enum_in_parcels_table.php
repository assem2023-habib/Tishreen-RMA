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
        Schema::table('parcels', function (Blueprint $table) {
            $table->enum('parcel_status', \App\Enums\ParcelStatus::values())
                ->default(\App\Enums\ParcelStatus::PENDING->value)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
            // Revert to old values if necessary, but typically we keep the new status capability
            // Or define the old list explicitly if strict rollback is needed
        });
    }
};
