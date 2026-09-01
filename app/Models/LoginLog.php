<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginLog extends Model
{
    protected $fillable = [
        'member_id',
        'email',
        'member_name',
        'successful',
        'failure_reason',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'successful' => 'boolean',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public static function record(
        ?Member $member,
        ?string $email,
        bool $successful,
        ?string $failureReason = null,
    ): void {
        static::create([
            'member_id' => $member?->getKey(),
            'email' => $email ?: $member?->email,
            'member_name' => $member
                ? trim(($member->first_name ?? '').' '.($member->last_name ?? '')) ?: $member->email
                : null,
            'successful' => $successful,
            'failure_reason' => $failureReason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
