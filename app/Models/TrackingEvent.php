<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    protected $table = 'tracking_events';
    protected $fillable = ['order_id', 'event_name','tud_id','tracking_id','user_source_name','is_fired','event_fired_time','campaign_id','ad_id','adset_id','json_data','ip_address','user_agent','url','referrer','segment'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
