<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleAttachment extends Model
{
    protected $fillable = [
        'article_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function isVideo(): bool
    {
        if (str_starts_with(strtolower($this->mime_type), 'video/')) {
            return true;
        }

        return in_array(
            strtolower(pathinfo($this->original_name ?: $this->file_path, PATHINFO_EXTENSION)),
            ['mp4', 'webm', 'ogv', 'ogg'],
            true,
        );
    }
}
