<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'image',
        'is_homepage_show',
        'homepage_category',
        'display_order',
        'is_show_in_menu',
        'is_active',
    ];
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->with('childrenRecursive')
            ->withCount('products');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public function products()
    {
        return $this->belongsToMany(products::class, 'product_category');
    }
    public function segment()
    {
        return $this->morphOne(Segment::class, 'segmentable','segmentable_type','segmentable_id');
    }
        public function segments()
    {
        return $this->morphToMany(Segment::class, 'segmentable');
    }

    public function getImageUrl(): ?string
    {
        if (!$this->image) return null;
        return (str_starts_with($this->image, 'http') || str_starts_with($this->image, '/'))
            ? $this->image
            : asset('images/category/' . $this->image);
    }

}
