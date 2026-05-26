<?php

namespace App\Support;

class DataLayer
{
    public static function item($product, int $quantity = 1, ?float $price = null): array
    {
        return [
            'item_id' => (string) ($product->id ?? ''),
            'item_name' => $product->name ?? '',
            'item_brand' => $product->brand->name ?? null,
            'item_category' => $product->category->name ?? null,
            'item_variant' => $product->variant_name ?? null,
            'price' => $price ?? (float) ($product->price ?? 0),
            'quantity' => $quantity,
        ];
    }

    public static function items($items): array
    {
        return collect($items)->map(function ($item) {
            return [
                'item_id' => (string) ($item->product_id ?? $item->id ?? ''),
                'item_name' => $item->product_name ?? $item->name ?? '',
                'item_brand' => $item->brand_name ?? null,
                'item_category' => $item->category_name ?? null,
                'item_variant' => $item->variant_name ?? null,
                'price' => (float) ($item->price ?? 0),
                'quantity' => (int) ($item->quantity ?? 1),
            ];
        })->values()->all();
    }
}
