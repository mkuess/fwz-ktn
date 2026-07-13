<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

/**
 * WICHTIG: Falls ihr über Filament bereits eine Organisation-Ressource/-Model
 * gebaut habt, NICHT diese Datei einfach drüberkopieren, sondern die Feldnamen
 * unten mit eurem bestehenden Model/Tabelle abgleichen und nur die fehlenden
 * Teile (z. B. $fillable-Einträge, die Passwort-Hashing-Logik) übernehmen.
 */
class Organisation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'zvr_number',
        'email',
        'password',
        'description',
        'logo_path',
        'street',
        'zip',
        'city',
        'phone',
        'website',
        'representative',
        'contact_person',
        'newsletter_optin',
        'access_code',
        'is_registered',
        'is_approved',
        'is_active',
        'approved_at',
        'approved_by',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'newsletter_optin' => 'boolean',
        'is_registered' => 'boolean',
        'is_approved' => 'boolean',
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Organisation $organisation) {
            if ($organisation->password && ! str_starts_with($organisation->password, '$2y$')) {
                $organisation->password = Hash::make($organisation->password);
            }
        });
    }
}
