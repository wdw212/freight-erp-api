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
        Schema::table('loading_addresses', static function (Blueprint $table) {
            $table->decimal('freight', 10)->comment('运费')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loading_addresses', static function (Blueprint $table) {
            $table->dropColumn('freight');
        });
    }
};
