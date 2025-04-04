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
        Schema::create('orders', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_type_id')->comment('业务类型ID')->default(0);
            $table->unsignedBigInteger('shipping_company_id')->comment('船公司')->default(0);
            $table->unsignedBigInteger('business_user_id')->comment('业务员ID')->default(0);
            $table->unsignedBigInteger('operation_user_id')->comment('操作员ID')->default(0);
            $table->unsignedBigInteger('document_user_id')->comment('单证员ID')->default(0);
            $table->unsignedBigInteger('commerce_user_id')->comment('商务ID')->default(0);
            $table->string('job_no')->comment('工作编号')->nullable();
            $table->string('contract_no')->comment('合同号')->nullable();
            $table->string('bl_no')->comment('提单号')->nullable();
            $table->string('origin_port')->comment('起运港')->nullable();
            $table->string('destination_port')->comment('目的港')->nullable();
            $table->string('ship_name')->comment('船名')->nullable();
            $table->string('ship_no')->comment('航次')->nullable();
            $table->string('container_type')->comment('柜型')->nullable();
            $table->tinyInteger('payment_method')->comment('付款方式 1月结 2付款买单')->default(0);
            $table->tinyInteger('cutoff_status')->comment('截单状态 1正常截单 2等通知截单 3开港后截单')->default(0);
            $table->string('sailing_schedule')->comment('船期')->nullable();
            $table->tinyInteger('bl_status')->comment('提单状态 1正常提单 2等通知电放 3已电放 4已seawaybil')->default(0);
            $table->tinyInteger('is_delivery')->comment('提货 0->未提货 1已提货')->default(0);
            $table->timestamp('sailing_at')->comment('开船时间')->nullable();
            $table->timestamp('arrival_at')->comment('到港时间')->nullable();
            $table->timestamp('finish_at')->comment('归属时间')->nullable();
            $table->timestamps();
            $table->comment('单据表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
