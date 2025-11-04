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
        Schema::create('invoices', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('订单ID');
            $table->unsignedBigInteger('invoice_type_id')->comment('发票类型');
            $table->string('email', 30)->comment('邮箱')->nullable();
            $table->string('remark')->comment('备注')->nullable();
            $table->string('cny_invoice_no')->comment('人民币发票号')->nullable();
            $table->string('usd_invoice_no')->comment('美金发票号')->nullable();
            $table->text('cny_remark')->comment('人民币备注')->nullable();
            $table->text('usd_remark')->comment('美金备注')->nullable();
            $table->date('invoice_date')->comment('开票日期')->nullable();
            $table->string('purchase_entity_id')->comment('购买方');
            $table->string('purchase_usc_code')->comment('购买方 统一社会信用代码');
            $table->json('purchase_entity')->comment('销售方 id usc_code')->nullable();
            $table->string('sale_entity_id')->comment('购买方');
            $table->string('sale_usc_code')->comment('购买方 统一社会信用代码');
            $table->timestamps();
            $table->comment('发票表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
