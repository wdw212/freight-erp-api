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
        Schema::table('orders', static function (Blueprint $table) {
            $table->string('payment_remark')->comment('应付款整单备注')->nullable();
            $table->decimal('payment_total_cny_amount', 10)->comment('应付款总计人民币')->default(0);
            $table->decimal('payment_total_usd_amount', 10)->comment('应付款总计美金')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', static function (Blueprint $table) {
            $table->dropColumn('payment_remark', 'payment_total_cny_amount', 'payment_total_usd_amount');
        });
    }
};
