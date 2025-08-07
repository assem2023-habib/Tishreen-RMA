<?php

use App\Enums\OperationTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('salary_histories', function (Blueprint $table) {
    //         $table->id();
    //         $table->json('old_row')->nullable();
    //         $table->json('new_row')->nullable();
    //         $table->enum('operation_type', OperationTypes::values())->default(OperationTypes::INSERTED->value);
    //         $table->timestamps();
    //     });
    // }

    /**
     * Reverse the migrations.
     */
    // public function down(): void
    // {
    //     Schema::dropIfExists('salary_histories');
    // }
};
