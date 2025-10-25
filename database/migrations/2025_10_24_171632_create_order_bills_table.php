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
        Schema::create('order_bills', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('单据ID');
            $table->string('delegation_header')->comment('委托人')->nullable();
            $table->string('job_no')->comment('工作编号')->nullable();
            $table->string('contract_no')->comment('合同号')->nullable();
            $table->string('bl_no')->comment('提单号')->nullable();
            $table->string('origin_port')->comment('起运港')->nullable();
            $table->string('destination_port')->comment('目的港')->nullable();
            $table->string('ship_name')->comment('船名')->nullable();
            $table->string('ship_no')->comment('航次')->nullable();
            $table->timestamp('sailing_at')->comment('开船时间')->nullable();
            $table->timestamp('arrival_at')->comment('到港时间')->nullable();
            $table->string('remark')->comment('备注')->nullable();
            $table->decimal('cny_amount', 10)->comment('人民币金额')->default(0);
            $table->decimal('usd_amount', 10)->comment('美金金额')->default(0);
            $table->timestamps();
            $table->comment('单据账单表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_bills');
    }
};
