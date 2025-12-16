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
        Schema::create('order_bill_items', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_bill_id')->comment('单据账单id');
            $table->unsignedBigInteger('fee_type_id')->comment('费用类型id')->nullable();
            $table->string('currency')->comment('货币: cny人民币 usd美金');
            $table->integer('quantity')->comment('数量')->default(1);
            $table->decimal('price', 10)->comment('单价')->nullable();
            $table->string('remark')->comment('备注')->nullable();
            $table->comment('单据账单-详情表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_bill_items');
    }
};
