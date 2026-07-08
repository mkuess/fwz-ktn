<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            if (! Schema::hasColumn('organisations', 'role')) {
                $table->enum('role', ['org_admin'])->default('org_admin')->after('type');
            }
        });

        // Backfill any existing organisations (created before this column existed).
        DB::table('organisations')->whereNull('role')->update(['role' => 'org_admin']);

        // The `role` column on `members` already exists as a plain nullable
        // string (added in an earlier migration). Backfill blank values,
        // then drop and recreate it as a proper enum with a default so new
        // records always get a valid role.
        DB::table('members')
            ->where(function ($query) {
                $query->whereNull('role')->orWhere('role', '');
            })
            ->update(['role' => 'member']);

        DB::table('members')
            ->whereNotIn('role', ['member', 'org_admin', 'admin'])
            ->update(['role' => 'member']);

        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'role')) {
                $table->dropColumn('role');
            }
        });

        Schema::table('members', function (Blueprint $table) {
            $table->enum('role', ['member', 'org_admin', 'admin'])->default('member')->after('newsletter_optin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            if (Schema::hasColumn('organisations', 'role')) {
                $table->dropColumn('role');
            }
        });

        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'role')) {
                $table->dropColumn('role');
            }
        });

        Schema::table('members', function (Blueprint $table) {
            $table->string('role')->nullable()->after('newsletter_optin');
        });
    }
};
