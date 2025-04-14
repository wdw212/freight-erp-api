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
        Schema::create('order_files', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('单据ID');
            $table->string('file')->comment('文件');
            $table->timestamps();
            $table->comment('单据-文件表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_files');
    }
};
