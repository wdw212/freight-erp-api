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
        Schema::create('operation_fee_items', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('operation_fee_id')->comment('操作费ID');
            $table->unsignedBigInteger('order_type_id')->comment('单据类型ID');
            $table->decimal('price', 10, 2)->comment('金额/票');
            $table->comment('操作费详情表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_fee_items');
    }
};
