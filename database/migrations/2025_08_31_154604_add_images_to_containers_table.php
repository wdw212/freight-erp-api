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
            $table->string('no_image')->comment('箱号图片')->nullable();
            $table->string('seal_number_image')->comment('封号图片')->nullable();
            $table->string('wharf_record_image')->comment('提箱记录图片')->nullable();
            $table->string('entered_port_record_image')->comment('进港记录图片')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('containers', static function (Blueprint $table) {
            $table->dropColumn('no_image', 'seal_number_image', 'wharf_record_image', 'entered_port_record_image');
        });
    }
};
