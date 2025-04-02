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
        Schema::create('usd_exchange_rates', static function (Blueprint $table) {
            $table->id();
            $table->string('month_code')->comment('月份标识，格式：YYYY-MM')->unique();
            $table->decimal('exchange_rate', 10)->comment('美元汇率（1美元对应本币金额）')->default(0);
            $table->timestamps();
            $table->comment('美金汇率表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usd_exchange_rates');
    }
};
