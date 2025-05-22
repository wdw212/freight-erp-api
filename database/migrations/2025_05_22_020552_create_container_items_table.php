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
        Schema::create('container_items', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('container_id')->comment('集装箱ID');
            $table->string('bl_no')->comment('提单号');
            $table->string('quantity')->comment('件数');
            $table->string('gross_weight')->comment('毛重');
            $table->string('volume')->comment('体积');
            $table->string('remark')->comment('备注')->nullable();
            $table->comment('集装箱详情表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('container_items');
    }
};
