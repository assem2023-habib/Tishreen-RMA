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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            
            // Polymorphic relation للمرسل (يمكن أن يكون User أو Employee)
            $table->morphs('sender');
            
            $table->text('content');
            $table->enum('type', ['text', 'image', 'file'])->default('text');
            $table->string('attachment_url')->nullable();
            $table->string('attachment_name')->nullable();
            
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index(['conversation_id', 'created_at']);
            $table->index('read_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
