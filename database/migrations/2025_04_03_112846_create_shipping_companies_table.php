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
        Schema::create('shipping_companies', static function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->integer('free_container_days')->comment('免用箱天数')->default(0);
            $table->string('tracking_url')->comment('查货网址')->nullable();
            $table->text('remark')->comment('备注')->nullable();
            $table->timestamps();
            $table->comment('船公司信息表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_companies');
    }
};
