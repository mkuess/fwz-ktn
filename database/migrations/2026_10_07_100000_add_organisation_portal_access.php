<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organisations', function (Blueprint $table): void {
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
        });

        Schema::table('members', function (Blueprint $table): void {
            $table->boolean('is_login_blocked')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('organisations', function (Blueprint $table): void {
            $table->dropColumn(['last_login_at', 'remember_token']);
        });
        Schema::table('members', function (Blueprint $table): void {
            $table->dropColumn('is_login_blocked');
        });
    }
};
