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
        Schema::create('page_annotations', static function (Blueprint $table) {
            $table->id();
            $table->string('model_type')->comment('页面');
            $table->text('content')->comment('内容');
            $table->comment('页面注明表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_annotations');
    }
};
