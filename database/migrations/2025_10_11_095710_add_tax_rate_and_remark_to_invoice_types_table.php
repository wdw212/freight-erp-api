<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoice_types', static function (Blueprint $table) {
            $table->decimal('tax_rate')->comment('税点')->default(0)->nullable();
            $table->string('remark')->comment('备注')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_types', static function (Blueprint $table) {
            $table->dropColumn('tax_rate','remark');
        });
    }
};
