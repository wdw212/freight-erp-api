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
        Schema::create('admin_users', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id')->comment('部门ID')->default(0);
            $table->string('name', 100)->comment('姓名');
            $table->string('username', 100)->comment('用户名');
            $table->string('password', 100)->comment('密码');
            $table->string('phone', 30)->comment('手机号')->nullable();
            $table->string('landline')->comment('座机')->nullable();
            $table->date('hire_date')->comment('入职时间')->nullable();
            $table->date('leave_date')->comment('离职时间')->nullable();
            $table->decimal('base_rate', 5)->comment('≤1万时的提成比例(%)')->default(0);
            $table->decimal('higher_rate', 5)->comment('＞1万时的提成比例(%)')->default(0);
            $table->integer('tickets')->comment('提成票数')->default(0);
            $table->decimal('unit_price')->comment('提成单价')->default(0);
            $table->decimal('basic_salary', 10)->comment('底薪工资')->default(0);
            $table->tinyInteger('status')->comment('状态 0禁用 1启用')->default(1);
            $table->timestamps();
            $table->comment('管理员表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users');
    }
};
