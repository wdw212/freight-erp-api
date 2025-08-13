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
        Schema::create('fee_types', static function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->string('type')->comment('类型 cny人民币 usd美金')->default('cny');
            $table->integer('sort')->comment('排序')->default(0);
            $table->comment('费用类型');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_types');
    }
};
