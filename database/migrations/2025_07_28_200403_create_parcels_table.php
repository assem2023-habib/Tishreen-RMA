<?php

use App\Enums\ParcelStatus;
use App\Enums\SenderType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parcels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id');
            $table->enum('sender_type', SenderType::values())->default(SenderType::GUEST_USER->value);
            $table->foreignId('route_id')->constrained('branch_routes')->cascadeOnDelete();
            $table->string('reciver_name');
            $table->string('reciver_address');
            $table->string('reciver_phone');
            $table->decimal('weight', 5, 3);
            $table->decimal('cost', 10, 3);
            $table->tinyInteger('is_paid')->default(0); // 0 من اجل ان يتم دفع الفاتورة من قبل المستلم
            $table->enum('parcel_status', ParcelStatus::values())->default(ParcelStatus::PENDING->value);
            $table->string('tracking_number')->unique();
            // $table->foreignId('price_policy_id')->constrained('pricing_policies')->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->cascadeOnDelete(); // ✅ ربط الطرد بالموعد
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcels');
    }
};
