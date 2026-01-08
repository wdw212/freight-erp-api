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
        Schema::create('social_securities', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('名称')->index();
            $table->string('id_card', 50)->comment('身份证号')->unique()->index();
            $table->string('phone', 50)->comment('手机号')->index();
            $table->string('person_type')->comment('人员类型: 1-内部人员 2-外部人员')->default(1);
            $table->decimal('adjusted_base', 10)->comment('调整后社保基数')->default(0);
            $table->decimal('company_makeup', 10)->comment('单位补缴')->default(0);
            $table->decimal('total_social_security', 10)->comment('社保总计')->default(0);
            $table->decimal('pension_company', 10)->comment('养老保险16%')->default(0);
            $table->decimal('unemployment_company', 10)->comment('失业保险0.5%')->default(0);
            $table->decimal('injury_company', 10)->comment('工伤保险0.5%')->default(0);
            $table->decimal('medical_company')->comment('医疗保险7.5%')->default(0);
            $table->decimal('serious_illness_company')->comment('大病保险1%')->default(0);
            $table->decimal('company_total')->comment('单位部分合计')->default(0);
            $table->decimal('pension_personal')->comment('养老保险8%')->default(0);
            $table->decimal('unemployment_personal')->comment('失业保险0.5%')->default(0);
            $table->decimal('medical_personal')->comment('医疗保险2%')->default(0);
            $table->decimal('personal_total', 10)->comment('个人部分合计')->default(0);
            $table->timestamps();
            $table->comment('社保表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_securities');
    }
};
