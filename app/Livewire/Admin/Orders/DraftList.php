<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use App\Models\Order_Item;
use App\Models\OrderDraft;
use Livewire\Component;
use Livewire\WithPagination;

class DraftList extends Component
{
    use WithPagination;

    public string $search     = '';
    public string $filterArea = '';

    public string $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        OrderDraft::cleanCompletedDrafts();
    }

    protected $queryString = [
        'search'     => ['except' => ''],
        'filterArea' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function createOrder(int $id): void
    {
        $draft = OrderDraft::with('items')->findOrFail($id);

        $order = Order::create([
            'name'             => $draft->name,
            'phone'            => $draft->phone,
            'email'            => $draft->email,
            'address'          => $draft->address,
            'delivery_area_id' => $draft->delivery_area_id,
            'payment_method'   => $draft->payment_method ?? 'cod',
            'subtotal'         => $draft->subtotal,
            'fee'              => $draft->delivery_charge,
            'discount'         => $draft->discount,
            'total'            => $draft->total,
            'notes'            => $draft->notes,
            'status'           => 'pending',
            'is_paid'          => false,
            'payment_status'   => 'unpaid',
            'cod_charge'       => 0,
            'cod_percentage'   => 0,
        ]);

        foreach ($draft->items as $item) {
            Order_Item::create([
                'order_id'   => $order->id,
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
                'price'      => $item->price,
                'total'      => $item->total,
                'options'    => $item->options ? json_encode($item->options) : null,
            ]);
        }

        $draft->delete();

        $this->dispatch('toast', [
            'icon'  => 'success',
            'title' => 'Order #' . $order->id . ' created successfully',
        ]);
    }

    public function deleteDraft(int $id): void
    {
        OrderDraft::findOrFail($id)->delete();
        $this->dispatch('toast', [
            'icon'  => 'success',
            'title' => 'Draft deleted',
        ]);
    }

    public function render()
    {
        $drafts = OrderDraft::with('items')
            ->when($this->search, fn ($q) =>
                $q->where('name', 'LIKE', "%{$this->search}%")
                  ->orWhere('phone', 'LIKE', "%{$this->search}%")
            )
            ->when($this->filterArea, fn ($q) =>
                $q->where('delivery_area_id', $this->filterArea)
            )
            ->latest()
            ->paginate(15);

        $totalDrafts   = OrderDraft::count();
        $expiredDrafts = OrderDraft::where('expires_at', '<', now())->count();

        return view('livewire.admin.orders.draft-list', compact(
            'drafts',
            'totalDrafts',
            'expiredDrafts',
        ));
    }
}
