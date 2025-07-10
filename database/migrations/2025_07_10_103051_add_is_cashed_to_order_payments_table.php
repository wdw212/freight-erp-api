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
        Schema::table('order_payments', static function (Blueprint $table) {
            $table->tinyInteger('cny_is_cashed')->comment('人民币是否兑付 0未兑付 1已兑付')->default(0);
            $table->tinyInteger('usd_is_cashed')->comment('美金是否兑付 0未兑付 1已兑付')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_payments', static function (Blueprint $table) {
            $table->dropColumn('cny_is_cashed', 'usd_is_cashed');
        });
    }
};
