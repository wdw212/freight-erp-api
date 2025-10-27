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
        Schema::table('order_bills', static function (Blueprint $table) {
            $table->text('cost_share')->comment('费用平摊')->nullable();
            $table->text('customer_payment_info')->comment('客户付款信息')->nullable();
            $table->text('company_receipt_info')->comment('公司收款信息')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_bills', function (Blueprint $table) {
            $table->dropColumn('cost_share', 'customer_payment_info', 'company_receipt_info');
        });
    }
};
