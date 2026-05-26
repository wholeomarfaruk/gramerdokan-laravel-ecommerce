<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    protected $table = 'segments';
    protected $fillable = ['name', 'slug', 'description', 'is_active', 'segmentable_id', 'segmentable_type'];

    public function products()
    {
        return $this->morphedByMany(products::class, 'segmentable');
    }

    public function categories()
    {
        return $this->morphedByMany(Category::class, 'segmentable');
    }
}
