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
        Schema::create('order_bl_infos', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('单据id');
            $table->string('bl_no')->comment('提单号')->nullable();
            $table->unsignedBigInteger('sender_id')->comment('发货人id')->nullable();
            $table->text('sender_info')->comment('发货人信息')->nullable();
            $table->unsignedBigInteger('receiver_id')->comment('收货人id')->nullable();
            $table->text('receiver_info')->comment('收货人信息')->nullable();
            $table->unsignedBigInteger('notifier_id')->comment('通知人id')->nullable();
            $table->text('notifier_info')->comment('通知人信息')->nullable();
            $table->text('freight_forwarding')->comment('货运代理')->nullable();
            $table->string('ship_name')->comment('船名')->nullable();
            $table->string('ship_no')->comment('航次')->nullable();
            $table->string('origin_port')->comment('起运港')->nullable();
            $table->string('destination_port')->comment('目的港')->nullable();
            $table->text('no')->comment('集装箱号、封条号、唛头号')->nullable();
            $table->text('number')->comment('包裹数量')->nullable();
            $table->text('description')->comment('货物描述')->nullable();
            $table->text('gross_weight')->comment('毛重')->nullable();
            $table->text('volume')->comment('体积')->nullable();
            $table->tinyInteger('freight_payment_method')->comment('运费付款方式 0预付 1到付')->default(0);
            $table->json('sender')->comment('发货人')->nullable();
            $table->json('receiver')->comment('收货人')->nullable();
            $table->json('notifier')->comment('通知人')->nullable();
            $table->timestamps();
            $table->comment('单据提单信息表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_bl_infos');
    }
};
