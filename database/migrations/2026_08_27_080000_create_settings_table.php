<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        $now = now();

        DB::table('settings')->insert([
            [
                'key' => 'member_registration_enabled',
                'value' => '1',
                'label' => 'Mitglieder-Anmeldung aktiv',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'organisation_registration_enabled',
                'value' => '1',
                'label' => 'Organisations-Anmeldung aktiv',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
