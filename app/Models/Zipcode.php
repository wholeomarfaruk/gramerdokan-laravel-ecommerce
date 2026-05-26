<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zipcode extends Model
{
    protected $fillable = ['name','code','slug', 'police_station_id'];
    public function police_station()
    {
        return $this->belongsTo(PoliceStation::class, 'police_station_id');
    }
    public function area_keywords()
    {
        return $this->hasMany(AreaKeyword::class, 'zipcode_id');
    }
}
