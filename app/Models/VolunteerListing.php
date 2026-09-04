<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VolunteerListing extends Model
{
    protected $fillable = [
        'organisation_id',
        'title',
        'description',
        'website_link',
        'flyer_path',
        'is_spontaneous',
        'weekdays',
        'daytimes',
        'hours_per_week',
        'street',
        'zip',
        'city',
        'valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'weekdays' => 'array',
            'daytimes' => 'array',
            'is_spontaneous' => 'boolean',
            'valid_until' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('valid_until')
                    ->orWhereDate('valid_until', '>=', today());
            })
            ->whereHas('organisation', fn (Builder $query): Builder => $query
                ->where('is_approved', true)
                ->where('is_active', true));
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(VolunteerListingCategory::class, 'volunteer_listing_category');
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(VolunteerListingActivity::class, 'volunteer_listing_activity');
    }
}
