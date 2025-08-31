<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('parcel_histories', function (Blueprint $table) {
            $table->dropForeign(['parcel_id']);
            $table->integer('parcel_id')->change();
        });
    }

    public function down()
    {
        Schema::table('parcel_histories', function (Blueprint $table) {
            $table->dropForeign(['parcel_id']);
            $table->dropColumn('parcel_id');
        });
    }
};
