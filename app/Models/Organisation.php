<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'is_approved',
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
        ];
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
