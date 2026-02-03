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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('set null');
            
            // Polymorphic relation للربط بأي كائن (طرد، فرع، إلخ)
            $table->nullableMorphs('related');
            
            $table->string('subject')->nullable(); // موضوع المحادثة
            $table->enum('status', ['pending', 'open', 'closed'])->default('pending');
            // pending = في انتظار موظف، open = جارية، closed = مغلقة
            
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('taken_at')->nullable(); // وقت أخذ المحادثة من قبل الموظف
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['status', 'created_at']);
            $table->index('customer_id');
            $table->index('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
