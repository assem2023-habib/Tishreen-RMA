<?php

use App\Enums\DaysOfWeek;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branch_routes', function (Blueprint $table) {

            $table->id();
            $table->foreignId('from_branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('to_branch_id')->constrained('branches')->cascadeOnDelete();
            //$table->enum('day', DaysOfWeek::values())->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->time('estimated_departur_time')->nullable();
            $table->time('estimated_arrival_time')->nullable();
            $table->decimal('distance_per_kilo', 11, 3)->nullable();
            $table->unique(['from_branch_id', 'to_branch_id', 'estimated_departur_time'],  'branch_routes_from_to_departure_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_routes');
    }
};
