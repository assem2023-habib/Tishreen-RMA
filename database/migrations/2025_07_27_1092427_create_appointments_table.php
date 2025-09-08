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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('route_id')->constrained('branch_routes')->cascadeOnDelete(); // الرحلة المرتبطة
            $table->date('date'); // تاريخ الموعد
            $table->time('time'); // وقت البداية للموعد
            $table->tinyInteger('capacity')->default(4); // عدد الطرود الممكن استيعابها في هذا الموعد
            $table->tinyInteger('booked_count')->default(0); // عدد الطرود المحجوزة حتى الآن
            $table->timestamps();

            $table->unique(['branch_id', 'date', 'time']); // لضمان عدم وجود موعد مكرر للفرع
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
