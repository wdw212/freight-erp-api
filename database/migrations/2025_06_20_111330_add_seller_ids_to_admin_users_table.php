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
        Schema::table('admin_users', static function (Blueprint $table) {
            $table->json('seller_ids')->comment('销货单位')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_users', static function (Blueprint $table) {
            $table->dropColumn('seller_ids');
        });
    }
};
