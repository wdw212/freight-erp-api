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
        Schema::create('order_bill_templates', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id')->comment('账号ID');
            $table->json('order_bill_items')->comment('账单详情')->nullable();
            $table->text('cost_share')->comment('费用平摊')->nullable();
            $table->text('customer_payment_info')->comment('客户付款信息')->nullable();
            $table->text('company_receipt_info')->comment('公司收款信息')->nullable();
            $table->timestamps();
            $table->comment('单据-账单-模版表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_bill_templates');
    }
};
