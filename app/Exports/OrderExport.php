<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrderExport implements FromCollection, WithHeadings, WithMapping
{
    protected $status;

    // Accept status in constructor
    public function __construct($status = null)
    {
        $this->status = $status;
    }
    /**
     * Return the data collection for the export
     */
    public function collection()
    {
        if ($this->status) {
            return Order::with('Order_Item')
                ->where('status', $this->status)
                ->select('id', 'name', 'address','phone', 'total', 'updated_at')
                ->get();
        }else{
            return Order::with('Order_Item')->select('id', 'name','phone', 'address', 'total', 'updated_at')->get();
        }
    }

    public function map($order): array
    {
        $items = '';
        $size = '';
    if($order->Order_Item->count() > 1){


        foreach ($order->Order_Item as $item) {
            $options = json_decode($item->options, true);
            $size = $options['size'] ?? '';
            $size2 = $options['size'] ? ' ('.$options['size'].') ': '';
            $items .= $item->product->name.$size2.' - ' . $item->quantity . " Qty ,\n";
            if ($size) {
                $size .= $options['size'].', ';
            }
        }
    }elseif($order->Order_Item->count() == 1){
        $firstItem = $order->Order_Item->first();
        $options = json_decode($firstItem->options, true);
        $size = $options['size'] ?? '';
        $items .= $firstItem->product->name .' ('.$size.')'. ' x ' . $firstItem->quantity . ' Qty';

    }else{
        $items = 'No items';
        $size = '';
    }


        return [
            $order->updated_at,
            $order->id,
            $order->name,
            $order->phone,
            $order->address,
            $items ?? '',        // Item Description
            $size ?? '',     // Size
            $order->total,
        ];
    }



    /**
     * Return the header row for the Excel file
     */
    public function headings(): array
    {
        return ['Date', 'ID', 'Customer Name', 'Phone','Address', 'Item Description', 'Size','Total'];
    }
}
