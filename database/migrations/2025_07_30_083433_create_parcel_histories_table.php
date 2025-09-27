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
    public function up(): void
    {
        Schema::create('parcel_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcel_id')->nullable()->constrained('parcels');
            $table->foreignId('user_id')->constrained('users');
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->json('changes')->nullable();
            $table->enum('operation_type', OperationTypes::values())->default(OperationTypes::INSERTED);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_histories');
    }
};
