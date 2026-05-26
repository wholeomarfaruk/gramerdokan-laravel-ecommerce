<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Pathao\PathaoService;
use Illuminate\Support\Facades\Log;


class OrderList extends Component
{
    use WithPagination;
    public $search = "";
    public $order_status;
    public $response;
    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'order_status' => ['except' => '']
    ];
    public $paginationTheme = 'bootstrap';


    public function render()
    {
        $orders = Order::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'LIKE', '%' . $this->search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $this->search . '%');
            })
            ->when($this->order_status, function ($query) {
                $query->where('status', $this->order_status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        $status_group = Order::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->get();
        $orders_count = Order::count();
        return view('livewire.admin.orders.order-list', compact('orders', 'status_group', 'orders_count'));
    }

    public function createPathaoOrder($order_id, PathaoService $pathao)
    {

        if (!$order_id) {
            return;
        }
        $order = Order::find($order_id);
        if (!$order) {
            return;
        }
        try {

            $response = $pathao->createOrder([
                "store_id" => 35021,
                "merchant_order_id" => $order->id,
                "recipient_name" => $order->name,
                "recipient_phone" => $order->phone,
                "recipient_address" => $order->address,
                "recipient_city" => 1,
                "recipient_zone" => 1,
                "recipient_area" => 1,
                "delivery_type" => 48,
                "item_type" => 2,
                "item_quantity" => $order->Order_Item->sum('quantity'),
                "item_weight" => $order->Order_Item->sum('quantity') * 0.5,
                "amount_to_collect" => (int) $order->total,
                "item_description" => json_encode($order->Order_Item->map(function ($item) {
                    return [
                        'name' => $item->product?->name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                }))
            ]);
            $this->dispatch('toast', [
                'title' => 'Success',
                'message' => 'Pathao order created',
                'icon' => 'success'
            ]);

            Log::info('Pathao order created', [
                'order_id' => $order->id,
                'pathao_response' => $response
            ]);
            if($response && isset($response['type']) && $response['type'] === 'success') {
            $order->status = 'processing';

            }
            $data = $order->json_data;
            $data['pathao'][] = [
                'timestamp' => now()->toDateTimeString(),
                'response'  => $response
            ];

            $order->json_data = $data;

            $order->save();
        } catch (\Throwable $e) {

            Log::error($e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Error',
                'message' => $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }
}
