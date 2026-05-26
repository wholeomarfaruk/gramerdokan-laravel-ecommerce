<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
