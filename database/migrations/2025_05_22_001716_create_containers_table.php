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
        Schema::create('containers', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('订单ID');
            $table->unsignedBigInteger('container_type_id')->comment('柜型ID');
            $table->string('no')->comment('箱号');
            $table->string('seal_number')->comment('封号');
            $table->unsignedBigInteger('pre_pull_wharf_id')->comment('预提');
            $table->unsignedBigInteger('wharf_id')->comment('提箱码头');
            $table->unsignedBigInteger('drop_off_wharf_id')->comment('落箱');
            $table->tinyInteger('is_entered_port')->comment('是否进港 0否 1是')->default(0);
            $table->string('driver')->comment('司机信息')->nullable();
            $table->unsignedBigInteger('fleet_id')->comment('车队ID')->nullable();
            $table->string('cargo_weight')->comment('货物重量')->nullable();
            $table->timestamp('loading_at')->comment('装柜时间')->nullable();
            $table->timestamps();
            $table->comment('订单-集装箱表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
