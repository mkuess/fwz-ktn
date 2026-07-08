<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerListing extends Model
{
    protected $fillable = [
        'organisation_id',
        'title',
        'description',
        'website_link',
        'is_spontaneous',
        'street',
        'zip',
        'city',
        'valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_spontaneous' => 'boolean',
            'valid_until' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }
}
