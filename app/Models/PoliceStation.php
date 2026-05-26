<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoliceStation extends Model
{
    protected $fillable = ['name', 'slug', 'city_id'];
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function zipcodes()
    {
        return $this->hasMany(Zipcode::class, 'police_station_id');
    }
}
