<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    use HasFactory;
    protected $appends = ['discounted_price', 'url'];
    public function orderItems()
    {
        return $this->hasMany(Order_Item::class);
    }
    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
    public function getFeaturedImageAttribute()
    {
        $media = $this->media?->where('category', 'featured_image')->first();

        if ($media && file_exists(public_path('uploads/' . $media->path))) {
            return asset('uploads/' . $media->path);
        }

        return asset('website/img/thumbnails/featured_img.jpg');
    }
    public function sizes()
    {
        return $this->hasMany(Size::class);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }
    // Product.php


    // Segment.php
    public function segments()
    {
        return $this->morphToMany(Segment::class, 'segmentable');
    }

    // accessor
    public function getSegmentAttribute()
    {
        return $this->segments()->first();
    }
    public function getDiscountedPriceAttribute()
    {
        // return min($this->discount_price, $this->price);
        return $this->discount_price ?? $this->price;
    }
    public function getUrlAttribute()
    {
      if($this->is_redirected){
        return $this->redirect_url;
      }
      return route('product.show', ['slug'=>$this->slug,'segment'=>$this->segment->slug]);
    }

    public function getImageThumbUrl(): ?string
    {
        if (!$this->image) return null;
        return (str_starts_with($this->image, 'http') || str_starts_with($this->image, '/'))
            ? $this->image
            : asset('storage/images/products/thumbnails/' . $this->image);
    }

    public function getImageFullUrl(): ?string
    {
        if (!$this->image) return null;
        return (str_starts_with($this->image, 'http') || str_starts_with($this->image, '/'))
            ? $this->image
            : asset('storage/images/products/' . $this->image);
    }

}
