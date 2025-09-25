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
        Schema::create('parcel_shipment_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcel_id')->constrained('parcels');
            $table->foreignId('shipment_id')->constrained('shipments');

            $table->foreignId('pick_up_confirmed_by_emp_id')->constrained('employees');
            $table->dateTime('pick_up_confirmed_date');

            $table->foreignId('delivery_confirmed_by_emp_id')->nullable()->constrained('employees');
            $table->dateTime('delivery_confirmed_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_shipment_assignments');
    }
};
