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
        Schema::create('harbors', static function (Blueprint $table) {
            $table->id();
            $table->string('code')->comment('代码');
            $table->string('name')->comment('名称');
            $table->string('country')->comment('国家');
            $table->string('route')->comment('航线');
            $table->timestamps();
            $table->comment('港口表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harbors');
    }
};
