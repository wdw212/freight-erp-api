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
        Schema::table('invoices', static function (Blueprint $table) {
            $table->decimal('total_cny_amount')->comment('人民币金额')->default(0);
            $table->decimal('total_usd_amount')->comment('美金金额')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', static function (Blueprint $table) {
            $table->dropColumn('total_cny_amount');
            $table->dropColumn('total_usd_amount');
        });
    }
};
