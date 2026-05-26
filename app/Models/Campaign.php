<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $table = 'campaigns';
    protected $fillable = [
        'name',
        'slug',
        'landing_page_id',
        'status',
        'json_data',
        'view_file',
    ];
}
