<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name', 'slug','code'];
    public function states()
    {
        return $this->hasMany(State::class,'country_id');
    }
}
