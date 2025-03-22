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
        Schema::create('notice_tags', static function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->tinyInteger('status')->comment('状态 0禁用 1启用')->default(1);
            $table->comment('公告标签表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notice_tags');
    }
};
