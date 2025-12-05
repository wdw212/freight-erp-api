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
        Schema::table('invoice_templates', static function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_type_id')->comment('发票类型id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_templates', static function (Blueprint $table) {
            $table->dropColumn('invoice_type_id');
        });
    }
};
