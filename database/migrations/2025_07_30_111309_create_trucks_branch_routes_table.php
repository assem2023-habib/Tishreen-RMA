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
        Schema::create('trucks_branch_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('branch_routes')->cascadeOnDelete();
            $table->foreignId('truck_id')->constrained('trucks')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trucks_branch_routes');
    }
};
