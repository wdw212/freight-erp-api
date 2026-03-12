<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('containers', function (Blueprint $table) {
            $table->string('no')->nullable()->change();
            $table->string('seal_number')->nullable()->change();
        });

        Schema::table('container_items', function (Blueprint $table) {
            $table->string('bl_no')->nullable()->change();
            $table->string('quantity')->nullable()->change();
            $table->string('gross_weight')->nullable()->change();
            $table->string('volume')->nullable()->change();
        });

        Schema::table('container_loading_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('loading_address_id')->nullable()->default(null)->change();
            $table->string('address')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('containers', function (Blueprint $table) {
            $table->string('no')->nullable(false)->change();
            $table->string('seal_number')->nullable(false)->change();
        });

        Schema::table('container_items', function (Blueprint $table) {
            $table->string('bl_no')->nullable(false)->change();
            $table->string('quantity')->nullable(false)->change();
            $table->string('gross_weight')->nullable(false)->change();
            $table->string('volume')->nullable(false)->change();
        });

        Schema::table('container_loading_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('loading_address_id')->nullable(false)->default(0)->change();
            $table->string('address')->nullable(false)->change();
        });
    }
};
