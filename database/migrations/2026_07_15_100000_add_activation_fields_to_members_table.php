<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->timestamp('activation_sent_at')->nullable()->after('card_sent_at');
            $table->string('activation_token')->nullable()->unique()->after('activation_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['activation_sent_at', 'activation_token']);
        });
    }
};
