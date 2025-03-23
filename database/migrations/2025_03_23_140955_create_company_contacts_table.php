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
        Schema::create('company_contacts', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id')->comment('部门ID')->default(0);
            $table->string('name')->comment('名称');
            $table->string('short_number')->comment('短号')->nullable();
            $table->string('landline')->comment('座机')->nullable();
            $table->string('phone')->comment('手机')->nullable();
            $table->timestamps();
            $table->comment('公司通讯录表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_contacts');
    }
};
