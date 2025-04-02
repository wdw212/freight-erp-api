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
        Schema::create('special_tax_rates', static function (Blueprint $table) {
            $table->id();
            $table->string('month_code')->comment('月份标识，格式：YYYY-MM')->unique();
            $table->decimal('one_amount', 10)->comment('金额小于等于')->default(0);
            $table->decimal('one_tax_rate', 10)->comment('税点')->default(0);
            $table->decimal('one_handling_fee', 10)->comment('手续费')->default(0);
            $table->decimal('two_amount', 10)->comment('金额小于等于')->default(0);
            $table->decimal('two_tax_rate', 10)->comment('税点')->default(0);
            $table->decimal('two_handling_fee', 10)->comment('手续费')->default(0);
            $table->timestamps();
            $table->comment('特殊费用税点表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_tax_rates');
    }
};
