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
        Schema::create('sellers', static function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->string('tax_number')->comment('纳税人识别号（唯一）')->unique();
            $table->string('phone')->comment('手机号');
            $table->string('address')->comment('地址');
            $table->string('bank_name')->comment('开户行')->nullable();
            $table->string('bank_account')->comment('开户账户')->nullable();
            $table->timestamps();
            $table->comment('销货单位表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
