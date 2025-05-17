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
        Schema::create('yard_wharves', static function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->comment('类型 0预提 1落箱')->default(0);
            $table->string('name')->comment('名称');
            $table->string('address')->comment('地址')->nullable();
            $table->decimal('amount', 10)->comment('金额')->default(0);
            $table->string('phone')->comment('手机号')->nullable();
            $table->text('remark')->comment('备注')->nullable();
            $table->integer('sort')->comment('排序')->default(0);
            $table->comment('预落堆场码头表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yard_wharves');
    }
};
