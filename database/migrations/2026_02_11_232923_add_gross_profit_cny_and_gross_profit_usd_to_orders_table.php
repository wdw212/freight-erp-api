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
            $table->decimal('gross_profit_cny', 10)->comment('毛利人民币')->default(0);
            $table->decimal('gross_profit_usd', 10)->comment('毛利美金')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', static function (Blueprint $table) {
            $table->dropColumn('gross_profit_cny', 'gross_profit_usd');
        });
    }
};
