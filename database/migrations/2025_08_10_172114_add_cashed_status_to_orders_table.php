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
            $table->tinyInteger('payment_cny_cashed_status')->comment('应付人民币-兑付状态 0未兑付 1部分兑付 2已兑付')->default(0);
            $table->tinyInteger('payment_usd_cashed_status')->comment('应付美金-兑付状态 0未兑付 1部分兑付 2已兑付')->default(0);
            $table->tinyInteger('receipt_cny_cashed_status')->comment('应收人民币-兑付状态 0未兑付 1部分兑付 2已兑付')->default(0);
            $table->tinyInteger('receipt_usd_cashed_status')->comment('应收美金-兑付状态 0未兑付 1部分兑付 2已兑付')->default(0);
            $table->tinyInteger('cashed_status')->comment('兑付状态 0未兑付 1部分兑付 2已兑付')->default(0);
            $table->tinyInteger('invoice_status')->comment('开票状态 0未开票 1部分开票 2已开票')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', static function (Blueprint $table) {
            $table->dropColumn('payment_cny_cashed_status');
            $table->dropColumn('payment_usd_cashed_status');
            $table->dropColumn('receipt_cny_cashed_status');
            $table->dropColumn('receipt_usd_cashed_status');
            $table->dropColumn('cashed_status');
            $table->dropColumn('invoice_status');
        });
    }
};
