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
        Schema::table('orders', static function (Blueprint $table) {
            $table->timestamp('operate_finish_at')->comment('操作归属时间')->nullable();
            $table->timestamp('commerce_finish_at')->comment('商务归属时间')->nullable();
            $table->decimal('usd_exchange_rate')->comment('美元汇率')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', static function (Blueprint $table) {
            $table->dropColumn('operate_finish_at');
            $table->dropColumn('commerce_finish_at');
            $table->dropColumn('usd_exchange_rate');
        });
    }
};
