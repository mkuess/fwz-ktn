<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organisation_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'email_verified_at',
        'membership_number',
        'membership_card_path',
        'source',
        'status',
        'approved_at',
        'approved_by',
        'newsletter_optin',
        'role',
        'street',
        'zip',
        'city',
    ];

    protected $hidden = [
        'password',
    ];

    protected $attributes = [
        'source' => 'self',
        'status' => 'pending',
        'role' => 'member',
        'newsletter_optin' => false,
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'newsletter_optin' => 'boolean',
        ];
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function managedOrganisations(): BelongsToMany
    {
        return $this->belongsToMany(Organisation::class, 'member_organisation')
            ->withPivot('can_edit')
            ->withTimestamps();
    }
}
