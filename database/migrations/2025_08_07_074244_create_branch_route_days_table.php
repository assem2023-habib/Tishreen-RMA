<?php

use App\Enums\DaysOfWeek;
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
        Schema::create('branch_route_days', function (Blueprint $table) {
            $table->id();
            $table->enum('day_of_week', DaysOfWeek::values());
            $table->foreignId('branch_route_id')->constrained('branches')->cascadeOnDelete();
            $table->unique(['day_of_week', 'branch_route_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_route_days');
    }
};
