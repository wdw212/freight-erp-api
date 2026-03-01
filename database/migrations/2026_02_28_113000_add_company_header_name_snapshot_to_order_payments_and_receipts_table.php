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
        Schema::table('order_payments', static function (Blueprint $table) {
            $table->string('company_header_name')
                ->nullable()
                ->after('company_header_id')
                ->comment('公司抬头名称快照');
        });

        Schema::table('order_receipts', static function (Blueprint $table) {
            $table->string('company_header_name')
                ->nullable()
                ->after('company_header_id')
                ->comment('公司抬头名称快照');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_payments', static function (Blueprint $table) {
            $table->dropColumn('company_header_name');
        });

        Schema::table('order_receipts', static function (Blueprint $table) {
            $table->dropColumn('company_header_name');
        });
    }
};
