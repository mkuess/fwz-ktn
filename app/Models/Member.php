<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Member extends Authenticatable
{
    use SoftDeletes;

    protected static function boot(): void
    {
        parent::boot();
        static::saved(function (Member $member) {
            if (empty($member->membership_number) && $member->id) {
                $member->updateQuietly([
                    'membership_number' => 'FWZ-' . now()->year . '-' . str_pad($member->id, 6, '0', STR_PAD_LEFT),
                ]);
            }
        });
    }

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
        'rejection_reason',
        'card_status',
        'card_sent_at',
        'activation_sent_at',
        'activation_token',
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
            'password'           => 'hashed',
            'email_verified_at'  => 'datetime',
            'approved_at'        => 'datetime',
            'newsletter_optin'   => 'boolean',
            'card_sent_at'       => 'date',
            'activation_sent_at' => 'datetime',
        ];
    }

    public function generateMembershipNumber(): string
    {
        $year   = now()->year;
        $padded = str_pad($this->id, 6, '0', STR_PAD_LEFT);
        return "FWZ-{$year}-{$padded}";
    }

    public function getFormattedMembershipNumberAttribute(): ?string
    {
        return $this->membership_number ?? null;
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
