<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Logical FK to cdb_admin.branches(branch_id) — that table is managed by the
            // raw SQL scripts under database/sql, not Laravel migrations, so no DB-level
            // FK constraint is declared here. Only used for role-scoping branch_mgr users.
            $table->unsignedBigInteger('branch_id')->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
};
