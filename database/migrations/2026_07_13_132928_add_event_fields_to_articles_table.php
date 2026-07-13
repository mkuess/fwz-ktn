<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('location')->nullable()->after('excerpt');
            $table->string('event_time')->nullable()->after('location');
            $table->string('organisation_name')->nullable()->after('event_time');
            $table->string('article_category')->nullable()->after('organisation_name');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['location', 'event_time', 'organisation_name', 'article_category']);
        });
    }
};
