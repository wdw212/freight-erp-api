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
        Schema::create('invoice_templates', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id')->comment('账号id');
            $table->string('name')->comment('模版名称');
            $table->string('email', 30)->comment('邮箱')->nullable();
            $table->string('remark')->comment('备注')->nullable();
            $table->text('cny_remark')->comment('人民币备注')->nullable();
            $table->text('usd_remark')->comment('美金备注')->nullable();
            $table->json('cny_invoice_items')->comment('人民币开票详情')->nullable();
            $table->json('usd_invoice_items')->comment('美金开票详情')->nullable();
            $table->string('purchase_entity_id')->comment('购买方');
            $table->string('purchase_usc_code')->comment('购买方 统一社会信用代码');
            $table->json('purchase_entity')->comment('销售方 id usc_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_templates');
    }
};
