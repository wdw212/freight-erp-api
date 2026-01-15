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
        Schema::create('accounts', static function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->comment('类型: public公账 private私账')->default(1);
            $table->decimal('cny_balance', 10)->comment('人民币余额')->default(0);
            $table->decimal('usd_balance', 10)->comment('美金余额')->default(0);
            $table->decimal('income_cny_deposit', 10)->comment('收入人民币押金')->default(0);
            $table->decimal('expense_cny_deposit', 10)->comment('支出人民币押金')->default(0);
            $table->decimal('income_usd_deposit', 10)->comment('收入美金押金')->default(0);
            $table->decimal('expense_usd_deposit', 10)->comment('支出美金押金')->default(0);
            $table->timestamps();
            $table->comment('账户表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
