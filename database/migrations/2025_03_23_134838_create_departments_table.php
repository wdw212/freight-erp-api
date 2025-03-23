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
        Schema::create('departments', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->comment('上级ID')->default(0);
            $table->string('name')->comment('名称');
            $table->comment('部门表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
