<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('special_cost_rates', static function (Blueprint $table) {
            $table->id();
            $table->string('month_code')->comment('月份标识，格式：YYYY-MM')->unique();
            $table->decimal('k_value')->comment('特殊费用计算比例系数，范围：0.0000 ~ 9.9999')->default(0);
            $table->comment('特殊费用比例表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_cost_rates');
    }
};
