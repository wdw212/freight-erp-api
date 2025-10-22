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
        Schema::table('orders', static function (Blueprint $table) {
            $table->decimal('gross_profit', 10)->comment('毛利润')->default(0);
            $table->decimal('special_fee', 10)->comment('特殊费用')->default(0);
            $table->decimal('commission', 10)->comment('佣金')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', static function (Blueprint $table) {
            $table->dropColumn('gross_profit', 'special_fee', 'commission');
        });
    }
};
