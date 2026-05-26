<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order_Item;
use App\Models\delivery_areas;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'subtotal',
        'discount',
        'total',
        'cod_charge',
        'cod_percentage',
        'fee',
        'name',
        'phone',
        'address',
        'delivery_area_id',
        'status',
        'is_paid',
        'delivery_date',
        'cancelled_date',
        'ip_address',
        'user_agent',
        'notes',
        'json_data',
        'customer_id',
        'device_id',
        'email',
        'payment_method',
        'transaction_id',
        'payment_status',
        'grand_total',
    ];
    public function Order_Item()
    {
        return $this->hasMany(Order_Item::class, 'order_id');
    }

    public function delivery_area()
    {
        return $this->belongsTo(delivery_areas::class, 'delivery_area_id');
    }
    public function customer()
    {
        return $this->belongsToMany(Customer::class, 'customer_order', 'order_id', 'customer_id')->withTimestamps()->limit(1);
    }

    public function trackingEvent()
    {
    return $this->hasOne(TrackingEvent::class)->withDefault();
    }

    public function getEventStatusAttribute()
    {
        $exist = $this->trackingEvent()->exists();
        if (!$exist) {
            return 'Event not created';
        }
        $status = $this->trackingEvent()->value('is_fired');
        if ($status) {
            return 'The Event is successfully fired';
        }
        return 'Event not fired';
    }
    public function getIsEventFiredAttribute()
    {
        $exist = $this->trackingEvent()->exists();
        if (!$exist) {
            return false;
        }
        $status = $this->trackingEvent()->value('is_fired');
        if ($status) {
            return true;
        }
        return false;
    }

    protected $casts = [
        'json_data' => 'array',
        'options' => 'array',
    ];
}
