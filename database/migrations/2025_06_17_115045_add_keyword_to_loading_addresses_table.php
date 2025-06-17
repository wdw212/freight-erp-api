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
        Schema::table('loading_addresses', static function (Blueprint $table) {
            $table->string('keyword')->comment('关键词(搜索使用)')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loading_addresses', static function (Blueprint $table) {
            $table->dropColumn('keyword');
        });
    }
};
