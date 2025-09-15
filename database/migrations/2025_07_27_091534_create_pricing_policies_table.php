<?php

use App\Enums\CurrencyType;
use App\Enums\PolicyTypes;
use App\Enums\PriceUnit;
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
        if (!Schema::hasTable('pricing_policies')) {
            Schema::create('pricing_policies', function (Blueprint $table) {
                $table->id();
                $table->enum('policy_type', PolicyTypes::values())->default(PolicyTypes::WEIGHT->value);
                $table->decimal('price', 10, 3); // السعر
                $table->enum('price_unit', PriceUnit::values())->default(PriceUnit::KG->value); // وحدة السعر مثلا KG , KM , PerParcel
                $table->decimal('limit_min', 7, 2)->nullable();
                $table->decimal('limit_max', 10, 2)->nullable();
                $table->enum('currency', CurrencyType::values())->nullable()->default(CurrencyType::SYRIA->value);
                $table->tinyInteger('is_active')->default(1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_policies');
    }
};
