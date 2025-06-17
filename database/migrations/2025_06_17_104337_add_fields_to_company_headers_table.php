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
            $table->json('operation_user_id')->comment('操作员id')->nullable();
            $table->json('document_user_id')->comment('单证员id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_headers', static function (Blueprint $table) {
            $table->dropColumn(['operation_user_id', 'document_user_id']);
        });
    }
};
