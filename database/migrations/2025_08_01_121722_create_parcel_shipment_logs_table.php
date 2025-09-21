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
        Schema::create('parcel_shipment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcel_id')->unique()->constrained('parcels')->cascadeOnDelete();
            $table->foreignId('pick_up_confirmed_by_emp_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('delivery_confirmed_by_emp_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('truck_id')->constrained('trucks');
            $table->dateTime('pick_up_confiremd_date'); // تاريخ تأكيد تسليم الطرد إلى الغرع
            $table->dateTime('delivery_confirmed_date')->nullable(); // تاريخ الذي تم فيه تسليم الطرد للعمي
            $table->dateTime('assigned_truck_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_shipment_logs');
    }
};
