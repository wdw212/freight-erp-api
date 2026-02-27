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
        Schema::table('admin_user_salaries', static function (Blueprint $table) {
            $table->string('job_type')->comment('工种类型，如：业务员、操作员、单证员等')->nullable()->after('admin_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_user_salaries', static function (Blueprint $table) {
            $table->dropColumn('job_type');
        });
    }
};
