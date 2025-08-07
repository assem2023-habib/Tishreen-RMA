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
        Schema::create('guest_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('address');
            $table->string('national_number');
            $table->date('birthday');
            $table->enum('user_type', ['Sender', 'Authorized'])->default('Sender');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_users');
    }
};
