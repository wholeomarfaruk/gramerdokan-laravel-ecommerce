<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $table = 'landing_pages';
    protected $fillable = [
        'name',
        'view_file',
        'status',
        'json_data',
        'version',
    ];
}
