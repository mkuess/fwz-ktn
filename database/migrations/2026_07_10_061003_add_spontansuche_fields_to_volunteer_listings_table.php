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
        Schema::table('volunteer_listings', function (Blueprint $table) {
            $table->json('weekdays')->nullable()->after('is_spontaneous');
            $table->json('daytimes')->nullable()->after('weekdays');
            $table->unsignedSmallInteger('hours_per_week')->nullable()->after('daytimes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteer_listings', function (Blueprint $table) {
            $table->dropColumn(['weekdays', 'daytimes', 'hours_per_week']);
        });
    }
};
