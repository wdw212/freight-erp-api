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
        Schema::create('invoice_items', static function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('类型 cny人民币 usd美金');
            $table->unsignedBigInteger('fee_type_id')->comment('费用类型id');
            $table->unsignedBigInteger('unit')->comment('单位')->nullable();
            $table->integer('number')->comment('数量')->default(1);
            $table->decimal('amount', 10)->comment('金额')->default(0);
            $table->comment('发票详情表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
