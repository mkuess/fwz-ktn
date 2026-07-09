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
        Schema::create('volunteer_listing_activity', function (Blueprint $table) {
            $table->foreignId('volunteer_listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('volunteer_listing_activity_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_listing_activity');
    }
};
