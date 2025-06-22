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
        Schema::table('regions', static function (Blueprint $table) {
            $table->decimal('nb_20_gp', 10)->comment('宁波20GP')->default(0);
            $table->decimal('nb_40_hq', 10)->comment('宁波40HQ')->default(0);
            $table->decimal('sh_20_gp', 10)->comment('上海20GP')->default(0);
            $table->decimal('sh_40_hq', 10)->comment('上海40HQ')->default(0);
            $table->string('remark')->comment('备注')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn('nb_20_gp', 'nb_40_hq', 'sh_20_gp', 'sh_40_hq', 'remark');
        });
    }
};
