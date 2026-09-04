<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insertOrIgnore([
            'key' => 'volunteer_listings_enabled',
            'value' => '1',
            'label' => 'Gesuche anzeigen',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')
            ->where('key', 'volunteer_listings_enabled')
            ->delete();
    }
};
