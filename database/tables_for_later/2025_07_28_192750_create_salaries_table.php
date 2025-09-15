<?php

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
    //     Schema::create('salaries', function (Blueprint $table) {
    //         $table->id();
    //         $table->decimal('salary', 8, 3);
    //         $table->foreignId('emp_id')->constrained('employees')->cascadeOnDelete();
    //         $table->decimal('reward', 5, 3)->nullable(); // الإضافات أو المكافات
    //         $table->decimal('discount', 5, places: 3)->nullable(); // الحسميات او العقوبات
    //         $table->string('notes')->nullable(); // من اجل إضافة السبب وراء الحسم او الاضافات على الرتب
    //         $table->enum('salary_status', ['', 'Not Active', ''])->default(''); // من اجل ان نختبر اذا تم قبض الراتب او لم يتم قبضه
    //         $table->date('validation_from')->nullable(); // صلاحية الراتب تبدا من هذا التاريخ
    //         $table->date('validation_to')->nullable(); // صلاحية الراتب تنتهي في هذا التاريخ
    //         $table->timestamps();
    //     });
    // }

    /**
     * Reverse the migrations.
     */
    // public function down(): void
    // {
    //     Schema::dropIfExists('salaries');
    // }
};
