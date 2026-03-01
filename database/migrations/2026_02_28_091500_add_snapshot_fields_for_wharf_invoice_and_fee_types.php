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
        Schema::table('containers', static function (Blueprint $table) {
            $table->string('pre_pull_wharf_name')->comment('预提码头名称快照')->nullable()->after('pre_pull_wharf_id');
            $table->string('wharf_name')->comment('提箱码头名称快照')->nullable()->after('wharf_id');
            $table->string('drop_off_wharf_name')->comment('落箱码头名称快照')->nullable()->after('drop_off_wharf_id');
        });

        Schema::table('orders', static function (Blueprint $table) {
            $table->string('entered_port_wharf_name')->comment('进港码头名称快照')->nullable()->after('entered_port_wharf_id');
        });

        Schema::table('invoices', static function (Blueprint $table) {
            $table->string('invoice_type_name')->comment('发票类型名称快照')->nullable()->after('invoice_type_id');
        });

        Schema::table('invoice_items', static function (Blueprint $table) {
            $table->string('fee_type_name')->comment('费用类型名称快照')->nullable()->after('fee_type_id');
        });

        Schema::table('order_bill_items', static function (Blueprint $table) {
            $table->string('fee_type_name')->comment('费用类型名称快照')->nullable()->after('fee_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('containers', static function (Blueprint $table) {
            $table->dropColumn([
                'pre_pull_wharf_name',
                'wharf_name',
                'drop_off_wharf_name',
            ]);
        });

        Schema::table('orders', static function (Blueprint $table) {
            $table->dropColumn('entered_port_wharf_name');
        });

        Schema::table('invoices', static function (Blueprint $table) {
            $table->dropColumn('invoice_type_name');
        });

        Schema::table('invoice_items', static function (Blueprint $table) {
            $table->dropColumn('fee_type_name');
        });

        Schema::table('order_bill_items', static function (Blueprint $table) {
            $table->dropColumn('fee_type_name');
        });
    }
};
