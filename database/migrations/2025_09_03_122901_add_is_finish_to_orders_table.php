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
            $table->tinyInteger('is_finish')->comment('是否结单 0否 1是')->default(0);
            $table->unsignedBigInteger('entered_port_wharf_id')->comment('进港码头')->nullable();
            $table->tinyInteger('insurance_id')->comment('保险 0不需要 1需要 2已做')->default(0);
            $table->tinyInteger('is_allowed')->comment('是否放行 0未放行 1已放行')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', static function (Blueprint $table) {
            $table->dropColumn([
                'is_finish',
                'entered_port_wharf_id',
                'insurance_id',
                'is_allowed',
            ]);
        });
    }
};
