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
        Schema::table('invoices', static function (Blueprint $table) {
            $table->tinyInteger('is_finish')->comment('单子完结快照 0否 1是')->default(0)->after('invoice_date');
            $table->decimal('commission', 10, 2)->comment('佣金快照')->default(0)->after('is_finish');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', static function (Blueprint $table) {
            $table->dropColumn([
                'is_finish',
                'commission',
            ]);
        });
    }
};
