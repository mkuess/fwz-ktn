<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Benefit extends Model
{
    protected $fillable = [
        'name',
        'description',
        'benefit_code',
        'content',
        'website',
        'logo_path',
        'is_active',
        'is_teaser',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_teaser' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
