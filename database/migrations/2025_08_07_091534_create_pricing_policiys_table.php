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
        Schema::create('pricing_policiys', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 10, 3);
            $table->string('price_unit');
            $table->decimal('weight_limit_min', 7, 2)->nullable();
            $table->decimal('weight_limit_max', 10, 2)->nullable();
            $table->string('currency')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_policiys');
    }
};
