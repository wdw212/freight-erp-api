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
        Schema::create('order_delegation_headers', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('订单ID');
            $table->unsignedBigInteger('seller_id')->comment('销货单位ID')->default(0);
            $table->unsignedBigInteger('company_header_id')->comment('公司抬头ID')->default(0);
            $table->string('contact_person')->comment('联系人')->nullable();
            $table->string('contact_phone')->comment('电话')->nullable();
            $table->json('remark')->comment('备注 json格式 联系方式、费用')->nullable();
            $table->timestamps();
            $table->comment('单据-委托抬头表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_delegation_headers');
    }
};
