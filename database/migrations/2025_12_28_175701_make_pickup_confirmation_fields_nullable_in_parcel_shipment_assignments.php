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
        Schema::table('parcel_shipment_assignments', function (Blueprint $table) {
            $table->foreignId('pick_up_confirmed_by_emp_id')->nullable()->change();
            $table->dateTime('pick_up_confirmed_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcel_shipment_assignments', function (Blueprint $table) {
            $table->foreignId('pick_up_confirmed_by_emp_id')->nullable(false)->change();
            $table->dateTime('pick_up_confirmed_date')->nullable(false)->change();
        });
    }
};
