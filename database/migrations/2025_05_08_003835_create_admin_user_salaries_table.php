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
        Schema::create('admin_user_salaries', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id')->comment('关联账号ID');
            $table->string('month_code')->comment('月份标识，格式：YYYY-MM')->unique();
            $table->decimal('basic_salary', 10)->comment('基本工资')->default(0);
            $table->decimal('base_rate', 5)->comment('≤1万时的提成比例(%)')->default(0);
            $table->decimal('higher_rate', 5)->comment('＞1万时的提成比例(%)')->default(0);
            $table->integer('tickets')->comment('提成票数')->default(0);
            $table->decimal('unit_price')->comment('提成单价')->default(0);
            $table->text('remark')->comment('备注')->nullable();
            $table->timestamps();
            $table->comment('员工-工资表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_user_salaries');
    }
};
