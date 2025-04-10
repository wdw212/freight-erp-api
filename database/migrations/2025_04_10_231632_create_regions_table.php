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
        Schema::create('regions', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->comment('上级ID')->index()->default(0);
            $table->string('name');
            $table->string('path')->comment('关系链 默认为 -')->default('-');
            $table->tinyInteger('level')->comment('0-省, 1-市, 2-区')->default(0);
            $table->comment('区域表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
