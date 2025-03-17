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
        Schema::create('company_types', static function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->tinyInteger('is_default')->comment('是否默认')->default(0);
            $table->comment('公司-类型表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_types');
    }
};
