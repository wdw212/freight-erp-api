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
        Schema::create('order_types', static function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->integer('sort')->comment('排序')->default(0);
            $table->comment('单据类型表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_types');
    }
};
