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
        Schema::create('sft_records', static function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('类型: sender发货人 receiver收货人 notifier通知人');
            $table->string('type_content')->comment('类型内容')->nullable();
            $table->string('name')->comment('名称');
            $table->string('phone')->comment('手机号');
            $table->string('url')->comment('舱单网址')->nullable();
            $table->tinyInteger('is_confirm')->comment('是否确认 0否 1是')->default(0);
            $table->unsignedBigInteger('confirm_user_id')->comment('确认人')->nullable();
            $table->json('operation_user_ids')->comment('操作员ids')->nullable();
            $table->json('document_user_ids')->comment('单证员ids')->nullable();
            $table->json('commerce_user_ids')->comment('商务ids')->nullable();
            $table->text('generate_information')->comment('生成信息')->nullable();
            $table->text('remark')->comment('备注')->nullable();
            $table->string('code')->comment('编码')->nullable();
            $table->string('address')->comment('地址')->nullable();
            $table->string('country')->comment('国家')->nullable();
            $table->string('aeo_company_code')->comment('aeo企业编码')->nullable();
            $table->string('contact_name')->comment('联系人姓名')->nullable();
            $table->string('contact_phone')->comment('联系人电话')->nullable();
            $table->timestamps();
            $table->comment('收发通表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sft_records');
    }
};
