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
        Schema::create('transactions', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id')->comment('销货单位id');
            $table->unsignedBigInteger('category')->comment('分类');
            $table->string('title')->comment('名称');
            $table->string('account')->comment('账号')->nullable();
            $table->string('invoice_no')->comment('发票号')->nullable();
            $table->unsignedBigInteger('serial_number')->comment('序号')->default(0);
            $table->string('type', 30)->comment('类型: public公账 private私账')->default(1);
            $table->decimal('income_cny', 10)->comment('应收人民币')->nullable();
            $table->decimal('expense_cny', 10)->comment('应付人民币')->nullable();
            $table->decimal('income_usd', 10)->comment('应收美金')->nullable();
            $table->decimal('expense_usd', 10)->comment('应付美金')->nullable();
            $table->decimal('remark')->comment('备注')->nullable();
            $table->decimal('exchange_rate', 10)->comment('汇率')->nullable();
            $table->timestamps();
            $table->comment('收支记录表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
