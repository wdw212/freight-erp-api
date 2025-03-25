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
        Schema::create('company_contracts', static function (Blueprint $table) {
            $table->id();
            $table->string('no')->comment('合同编号')->unique();
            $table->unsignedBigInteger('seller_id')->comment('销货单位ID')->default(0);
            $table->unsignedBigInteger('company_header_id')->comment('公司抬头')->default(0);
            $table->tinyInteger('type')->comment('类型 1一代月结 2同行买单 3直客合同 4公司员工')->default(0);
            $table->string('phone')->comment('电话')->nullable();
            $table->timestamp('start_at')->comment('开始时间')->nullable();
            $table->timestamp('expire_at')->comment('到期时间')->nullable();
            $table->text('remark')->comment('备注')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_contracts');
    }
};
