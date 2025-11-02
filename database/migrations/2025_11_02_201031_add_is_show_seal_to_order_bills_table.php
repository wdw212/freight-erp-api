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
        Schema::table('order_bills', static function (Blueprint $table) {
            $table->tinyInteger('is_show_seal')->comment('是否显示公章 0否 1是')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_bills', static function (Blueprint $table) {
            $table->dropColumn('is_show_seal');
        });
    }
};
