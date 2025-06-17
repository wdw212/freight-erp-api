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
        Schema::table('company_headers', static function (Blueprint $table) {
            $table->json('company_type')->comment('公司类型 0委托 1应付 2应收')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_headers', static function (Blueprint $table) {
            $table->dropColumn('company_type');
        });
    }
};
