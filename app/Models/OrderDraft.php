<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderDraft extends Model
{
    protected $table = 'order_drafts';

    protected $fillable = [
        'device_id',
        'customer_id',
        'name',
        'phone',
        'email',
        'address',
        'delivery_area_id',
        'payment_method',
        'subtotal',
        'delivery_charge',
        'discount',
        'total',
        'notes',
        'expires_at',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'discount'        => 'decimal:2',
        'total'           => 'decimal:2',
        'expires_at'      => 'datetime',
        'options'         => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderDraftItem::class, 'draft_id');
    }

    /** Delete drafts that have a real (non-autosave) order matching by phone, device_id, or customer_id. */
    public static function cleanCompletedDrafts(): int
    {
        return static::where(function ($q) {
            $q->where(function ($q) {
                $q->whereNotNull('phone')
                    ->whereExists(fn($sub) => $sub->selectRaw('1')
                        ->from('orders')
                        ->whereColumn('orders.phone', 'order_drafts.phone')
                        ->where('orders.status', '!=', 'autosave'));
            })
            ->orWhere(function ($q) {
                $q->whereNotNull('device_id')
                    ->whereExists(fn($sub) => $sub->selectRaw('1')
                        ->from('orders')
                        ->whereColumn('orders.device_id', 'order_drafts.device_id')
                        ->where('orders.status', '!=', 'autosave'));
            })
            ->orWhere(function ($q) {
                $q->whereNotNull('customer_id')
                    ->whereExists(fn($sub) => $sub->selectRaw('1')
                        ->from('orders')
                        ->whereColumn('orders.customer_id', 'order_drafts.customer_id')
                        ->where('orders.status', '!=', 'autosave'));
            });
        })->delete();
    }
}
