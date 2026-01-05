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
            $table->unsignedBigInteger('origin_harbor_id')->comment('起运港id')->nullable();
            $table->json('origin_harbor')->comment('起运港')->nullable();
            $table->unsignedBigInteger('destination_harbor_id')->comment('目的港id')->nullable();
            $table->json('destination_harbor')->comment('目的港')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', static function (Blueprint $table) {
            $table->dropColumn('origin_harbor_id');
            $table->dropColumn('destination_harbor_id');
            $table->dropColumn('origin_harbor');
            $table->dropColumn('destination_harbor');
        });
    }
};
