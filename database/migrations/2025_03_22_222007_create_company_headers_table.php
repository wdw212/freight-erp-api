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
        Schema::create('company_headers', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_type_id')->comment('公司类型ID');
            $table->unsignedBigInteger('admin_user_id')->comment('公司业务员ID')->default(0);
            $table->string('company_name')->comment('公司名称');
            $table->string('tax_number')->comment('税号')->nullable();
            $table->string('billing_address')->comment('开票地址')->nullable();
            $table->string('company_phone')->comment('公司电话（座机）')->nullable();
            $table->string('bank_name')->comment('开户行')->nullable();
            $table->string('bank_account')->comment('开户账户')->nullable();
            $table->string('delivery_phone')->comment('交付手机号')->nullable();
            $table->string('delivery_email')->comment('交付邮箱')->nullable();
            $table->string('contact_person')->comment('联系人')->nullable();
            $table->string('contact_phone')->comment('联系方式')->nullable();
            $table->string('qq')->comment('QQ')->nullable();
            $table->string('distinction')->comment('区分')->nullable();
            $table->string('delivery_address')->comment('寄件地址')->nullable();
            $table->longText('remark')->comment('备注')->nullable();
            $table->timestamps();
            $table->comment('公司抬头表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_headers');
    }
};
