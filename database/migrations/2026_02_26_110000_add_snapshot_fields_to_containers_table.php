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
            $table->string('container_type_name')->comment('柜型名称快照')->nullable()->after('container_type_id');
            $table->string('fleet_name')->comment('车队名称快照')->nullable()->after('fleet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('containers', static function (Blueprint $table) {
            $table->dropColumn(['container_type_name', 'fleet_name']);
        });
    }
};
