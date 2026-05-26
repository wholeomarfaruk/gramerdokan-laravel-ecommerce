<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaKeyword extends Model
{
    protected $fillable = ['name', 'slug', 'zipcode_id'];
    public function zipcode()
    {
        return $this->belongsTo(Zipcode::class);
    }

}
