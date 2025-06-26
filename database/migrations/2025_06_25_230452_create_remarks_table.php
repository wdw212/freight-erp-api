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
        Schema::create('remarks', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id')->comment('账号ID');
            $table->text('content')->comment('内容');
            $table->timestamps();
            $table->comment('备注表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remarks');
    }
};
