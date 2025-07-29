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
        Schema::create('container_loading_addresses', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('container_id')->comment('集装箱ID');
            $table->unsignedBigInteger('loading_address_id')->comment('装柜地址ID')->default(0);
            $table->string('loading_address')->comment('装柜地址')->nullable();
            $table->string('address')->comment('地址');
            $table->string('contact_name')->comment('联系人')->nullable();
            $table->string('phone')->comment('联系方式')->nullable();
            $table->text('remark')->comment('备注')->nullable();
            $table->comment('集装箱-装柜地址表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('container_loading_addresses');
    }
};
