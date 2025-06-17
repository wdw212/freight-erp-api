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
            $table->dropColumn('company_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_type_id', static function (Blueprint $table) {
            $table->unsignedBigInteger('company_type_id')->comment('公司类型ID');
        });
    }
};
