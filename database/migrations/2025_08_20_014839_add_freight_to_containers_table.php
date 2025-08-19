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
            $table->tinyInteger('freight_status')->comment('运费情况 0月结 1现金未付 2现金已付 3驮鸟未确认 4驮鸟已确认')->default(0);
            $table->string('freight_remark')->comment('运费备注')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('containers', static function (Blueprint $table) {
            $table->dropColumn('freight_status');
            $table->dropColumn('freight_remark');
        });
    }
};
