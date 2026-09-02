<?php

namespace App\Models;

use App\Services\GeocodingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organisation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'role',
        'name',
        'zvr_number',
        'email',
        'password',
        'description',
        'logo_path',
        'street',
        'zip',
        'city',
        'latitude',
        'longitude',
        'geocoded_at',
        'phone',
        'website',
        'representative',
        'contact_person',
        'is_approved',
        'approval_status',
        'rejection_reason',
        'is_active',
        'approved_at',
        'approved_by',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_approved' => 'boolean',
            'is_active' => 'boolean',
            'approved_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
            'geocoded_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Organisation $organisation): void {
            if (! $organisation->isDirty('approval_status') || blank($organisation->approval_status)) {
                return;
            }

            $isApproved = $organisation->approval_status === 'approved';
            $organisation->is_approved = $isApproved;
            $organisation->approved_at = $isApproved
                ? ($organisation->approved_at ?? now())
                : null;

            if ($organisation->approval_status !== 'rejected') {
                $organisation->rejection_reason = null;
            }
        });

        static::saved(function (Organisation $organisation): void {
            $addressChanged = $organisation->wasRecentlyCreated
                || $organisation->wasChanged(['street', 'zip', 'city']);
            $hasCoordinates = $organisation->latitude !== null
                && $organisation->longitude !== null;

            if (! $addressChanged && $hasCoordinates) {
                return;
            }

            if ($addressChanged && $hasCoordinates) {
                $organisation->updateQuietly([
                    'latitude' => null,
                    'longitude' => null,
                    'geocoded_at' => null,
                ]);
            }

            if (blank($organisation->city) && blank($organisation->zip)) {
                return;
            }

            $organisationId = $organisation->getKey();

            dispatch(static function () use ($organisationId): void {
                $organisation = Organisation::query()->find($organisationId);

                if ($organisation === null) {
                    return;
                }

                app(GeocodingService::class)->geocodeOrganisation($organisation);
            })->afterResponse();
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'organisation_category');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function volunteerListings(): HasMany
    {
        return $this->hasMany(VolunteerListing::class);
    }
}
