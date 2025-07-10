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
        Schema::create('order_payments', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('关联单据ID');
            $table->unsignedBigInteger('company_header_id')->comment('公司抬头ID');
            $table->string('no_invoice_remark')->comment('不开票备注')->nullable();
            $table->decimal('cny_amount', 10)->comment('人民币费用')->default(0);
            $table->string('cny_invoice_number')->comment('人民币发票号')->nullable();
            $table->decimal('usd_amount', 10)->comment('美金费用')->default(0);
            $table->string('usd_invoice_number')->comment('美金发票号')->nullable();
            $table->string('contact_person')->comment('联系人')->nullable();
            $table->string('contact_phone')->comment('联系方式')->nullable();
            $table->longText('remark')->comment('备注')->nullable();
            $table->timestamps();
            $table->comment('单据-应付款表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
