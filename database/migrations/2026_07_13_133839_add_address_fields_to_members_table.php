<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('street')->nullable()->after('email');
            $table->string('zip', 10)->nullable()->after('street');
            $table->string('city')->nullable()->after('zip');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['street', 'zip', 'city']);
        });
    }
};
