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
            $table->dropColumn('business_user_id', 'operation_user_id', 'document_user_id');
            $table->json('business_user_ids')->comment('业务员ids')->nullable();
            $table->json('operation_user_ids')->comment('操作员ids')->nullable();
            $table->json('document_user_ids')->comment('单证员ids')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loading_addresses', static function (Blueprint $table) {
            $table->unsignedBigInteger('business_user_id')->comment('业务员ID')->default(0);
            $table->unsignedBigInteger('operation_user_id')->comment('操作员ID')->default(0);
            $table->unsignedBigInteger('document_user_id')->comment('单证员ID')->default(0);
            $table->dropColumn(['business_user_ids', 'operation_user_ids', 'document_user_ids']);
        });
    }
};
