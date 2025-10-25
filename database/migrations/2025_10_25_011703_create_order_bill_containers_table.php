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
        Schema::create('order_bill_containers', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_bill_id')->comment('单据账单id');
            $table->string('no')->comment('箱号');
            $table->string('container_type')->comment('柜型')->nullable();
            $table->string('driver')->comment('司机');
            $table->comment('单据账单-箱子表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_bill_containers');
    }
};
