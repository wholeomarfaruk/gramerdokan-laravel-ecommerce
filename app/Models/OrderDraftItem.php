<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDraftItem extends Model
{
    protected $table = 'order_draft_items';

    protected $fillable = [
        'draft_id',
        'product_id',
        'product_name',
        'product_image',
        'quantity',
        'price',
        'total',
        'options',
    ];

    protected $casts = [
        'price'   => 'decimal:2',
        'total'   => 'decimal:2',
        'options' => 'array',
    ];

    public function draft(): BelongsTo
    {
        return $this->belongsTo(OrderDraft::class, 'draft_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(products::class, 'product_id');
    }
}
