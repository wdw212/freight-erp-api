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
        Schema::create('drivers', static function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->comment('车牌号');
            $table->string('name')->comment('名称');
            $table->string('phone')->comment('电话');
            $table->text('remark')->comment('备注')->nullable();
            $table->timestamps();
            $table->comment('司机表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
