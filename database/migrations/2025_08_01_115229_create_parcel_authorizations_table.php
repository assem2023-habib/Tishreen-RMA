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
        Schema::create('parcel_authorizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parcel_id')->constrained('parcels')->cascadeOnDelete();
            $table->foreignId('authorized_user_id');
            $table->enum('authorized_user_type', ['Authorized', 'Guest']);
            $table->string('authorized_code')->unique();
            $table->enum('authorized_status', ['Pending', 'Active', 'Used', 'Canceled', 'Expired']);
            $table->dateTime('generated_at')->default(now());
            $table->dateTime('expired_at')->nullable();
            $table->dateTime('used_at')->nullable();
            $table->string('cancellation_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_authorizations');
    }
};
