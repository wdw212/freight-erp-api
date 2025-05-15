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
        Schema::create('operation_fees', static function (Blueprint $table) {
            $table->id();
            $table->string('month_code')->comment('月份标识，格式：YYYY-MM')->unique();
            $table->decimal('profit_adjustment_amount', 10)->comment('利润调整金额')->default(0);
            $table->text('remark')->comment('备注')->nullable();
            $table->timestamps();
            $table->comment('操作费表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_fees');
    }
};
