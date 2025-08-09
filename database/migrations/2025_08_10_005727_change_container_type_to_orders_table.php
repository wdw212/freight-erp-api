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
            $table->dropColumn('container_type');
            $table->unsignedBigInteger('container_type_id')->comment('柜型ID')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', static function (Blueprint $table) {
            $table->dropColumn('container_type_id');
            $table->string('container_type')->comment('柜型')->nullable();
        });
    }
};
