<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('city');
            $table->enum('card_status', ['ausstehend', 'zugesendet'])->default('ausstehend')->after('rejection_reason');
            $table->date('card_sent_at')->nullable()->after('card_status');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'card_status', 'card_sent_at']);
        });
    }
};
