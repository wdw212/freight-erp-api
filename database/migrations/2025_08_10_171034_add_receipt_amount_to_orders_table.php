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
            $table->decimal('receipt_total_cny_amount', 10)->comment('应收总计人民币')->default(0);
            $table->decimal('receipt_total_usd_amount', 10)->comment('应收总计美金')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('receipt_total_cny_amount');
            $table->dropColumn('receipt_total_usd_amount');
        });
    }
};
