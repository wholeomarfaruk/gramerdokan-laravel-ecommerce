<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'filename',
        'original_name',
        'mime_type',
        'extension',
        'size',
        'type',
        'disk',
        'path',
        'category',
        'mediable_id',
        'mediable_type',
        'user_id',
        'caption',
        'json',
    ];

    protected $casts = [
        'json' => 'array',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function variants()
    {
        return $this->hasMany(Variant::class, 'media_id');
    }

    public function mediable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── URL helpers ─────────────────────────────────────────────────────────

    public function getUrl(): string
    {
        return asset('storage/' . ltrim($this->path, '/'));
    }

    public function getThumbnailUrl(): string
    {
        $thumb = $this->getVariant('thumb');

        if ($thumb) {
            return asset('storage/' . ltrim($thumb->path, '/'));
        }

        return $this->getUrl();
    }

    public function getVariant(string $ratio): ?Variant
    {
        return $this->variants->firstWhere('ratio', $ratio);
    }

    // ─── Metadata helpers ────────────────────────────────────────────────────

    public function readableSize(): string
    {
        $bytes = (int) $this->size;

        if ($bytes >= 1_073_741_824) {
            return number_format($bytes / 1_073_741_824, 2) . ' GB';
        }

        if ($bytes >= 1_048_576) {
            return number_format($bytes / 1_048_576, 2) . ' MB';
        }

        if ($bytes >= 1_024) {
            return number_format($bytes / 1_024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isDocument(): bool
    {
        return $this->type === 'document';
    }

    public function isAudio(): bool
    {
        return $this->type === 'audio';
    }
}
