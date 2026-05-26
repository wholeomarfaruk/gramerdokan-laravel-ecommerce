<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

    protected $fillable = [
        'customer_id',
        'device_id',
        'sub_total',
        'delivery_charge',
        'fee',
        'total',
        'discount',
        'grand_total',
        'coupon_code',
        'coupon_id',

    ];
     public function user()
    {
        return $this->belongsTo(Customer::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    public function subtotal(): float
    {
        return (float) $this->items->sum(fn (CartItem $item) => $item->price * $item->quantity);
    }

    public function totalItems(): int
    {
        return (int) $this->items->sum('quantity');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
