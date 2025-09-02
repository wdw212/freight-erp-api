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
        Schema::table('containers', static function (Blueprint $table) {
            $table->text('entered_port_info')->comment('进港数据')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('containers', static function (Blueprint $table) {
            $table->dropColumn('entered_port_info');
        });
    }
};
