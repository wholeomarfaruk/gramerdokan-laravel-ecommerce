<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $fillable = [
        'media_id',
        'ratio',
        'size',
        'path',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function main()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    // ─── URL helpers ─────────────────────────────────────────────────────────

    public function getUrl(): string
    {
        return asset('storage/' . ltrim($this->path, '/'));
    }
}
