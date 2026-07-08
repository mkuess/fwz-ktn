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
        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('organisation_id')->nullable()->change();

            if (! Schema::hasColumn('members', 'role')) {
                $table->string('role')->nullable()->after('newsletter_optin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'role')) {
                $table->dropColumn('role');
            }

            $table->foreignId('organisation_id')->nullable(false)->change();
        });
    }
};
