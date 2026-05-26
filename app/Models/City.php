<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'slug', 'state_id'];
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function ps()
    {
        return $this->hasMany(PoliceStation::class);
    }
}
