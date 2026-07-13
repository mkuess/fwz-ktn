<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * NUR AUSFÜHREN, WENN ES NOCH KEINE organisations-Tabelle GIBT.
 * Falls ihr die Filament-OrganisationResource bereits gebaut habt, existiert
 * diese Tabelle vermutlich schon — dann diese Migration NICHT laufen lassen,
 * sondern nur prüfen, ob die Spalten unten mit eurer bestehenden Tabelle
 * übereinstimmen (siehe Kommentare bei den Formularfeldern in den Views).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('organisations')) {
            return;
        }

        Schema::create('organisations', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['verein', 'organisation']);
            $table->string('name');
            $table->string('zvr_number', 20)->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('street')->nullable();
            $table->string('zip', 10)->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('representative')->nullable();
            $table->string('contact_person')->nullable();
            $table->boolean('newsletter_optin')->default(false);
            $table->string('access_code', 8)->nullable()->unique();
            $table->boolean('is_registered')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisations');
    }
};
