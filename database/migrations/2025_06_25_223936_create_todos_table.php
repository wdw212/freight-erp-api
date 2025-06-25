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
        Schema::create('todos', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id')->comment('账号ID');
            $table->string('title')->comment('标题');
            $table->tinyInteger('status')->comment('状态 0未完成 1已完成')->default(0);
            $table->timestamps();
            $table->comment('待办事项表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
