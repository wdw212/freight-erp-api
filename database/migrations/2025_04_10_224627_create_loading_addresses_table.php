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
        Schema::create('loading_addresses', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('region_id')->comment('地区ID');
            $table->string('address')->comment('装柜地址');
            $table->string('contact_name')->comment('联系人')->nullable();
            $table->string('phone')->comment('联系方式')->nullable();
            $table->text('remark')->comment('备注')->nullable();
            $table->unsignedBigInteger('business_user_id')->comment('业务员ID')->default(0);
            $table->unsignedBigInteger('operation_user_id')->comment('操作员ID')->default(0);
            $table->unsignedBigInteger('document_user_id')->comment('单证员ID')->default(0);
            $table->timestamps();
            $table->comment('装柜地址表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loading_addresses');
    }
};
