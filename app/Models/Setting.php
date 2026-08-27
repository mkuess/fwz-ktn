<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    private const CACHE_KEY = 'settings.values';

    protected $fillable = [
        'key',
        'value',
        'label',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value],
        );

        Cache::forget(self::CACHE_KEY);
    }

    public static function enabled(string $key, bool $default = true): bool
    {
        $values = Cache::rememberForever(self::CACHE_KEY, static fn (): array => static::query()
            ->pluck('value', 'key')
            ->all());
        $value = $values[$key] ?? ($default ? '1' : '0');

        return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
    }
}
