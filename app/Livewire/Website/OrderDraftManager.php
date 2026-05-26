<?php

namespace App\Livewire\Website;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\delivery_areas;
use App\Models\Device;
use App\Models\OrderDraft;
use App\Models\OrderDraftItem;
use Livewire\Attributes\Locked;
use Livewire\Component;

class OrderDraftManager extends Component
{
    // Tracked form fields — wire:model.live.debounce in view triggers updated()
    public string $name             = '';
    public string $phone            = '';
    public string $email            = '';
    public string $address          = '';
    public string $delivery_area_id = '';
    public string $payment_method   = 'cod';
    public string $notes            = '';

    // Internal state — not editable from outside
    #[Locked]
    public ?int $draftId = null;

    // Display-only totals
    public float $subtotal        = 0;
    public float $deliveryCharge  = 0;
    public float $total           = 0;

    // Auto-save status shown in UI: null | 'saving' | 'saved'
    public ?string $saveStatus = null;

    public function mount(): void
    {
        $deviceCookie = request()->cookie('_sfdid');
        if (! $deviceCookie) {
            return;
        }

        $existing = OrderDraft::where('device_id', $deviceCookie)
            ->latest()
            ->first();

        if (! $existing) {
            return;
        }

        $this->draftId          = $existing->id;
        $this->name             = $existing->name ?? '';
        $this->phone            = $existing->phone ?? '';
        $this->email            = $existing->email ?? '';
        $this->address          = $existing->address ?? '';
        $this->delivery_area_id = $existing->delivery_area_id ?? '';
        $this->payment_method   = $existing->payment_method ?? 'cod';
        $this->notes            = $existing->notes ?? '';
        $this->subtotal         = (float) $existing->subtotal;
        $this->deliveryCharge   = (float) $existing->delivery_charge;
        $this->total            = (float) $existing->total;
    }

    // Called automatically on every property change via wire:model.live
    public function updated(string $property): void
    {
        $this->autoSave();
    }

    public function autoSave(): void
    {
        $deviceCookie = request()->cookie('_sfdid');
        if (! $deviceCookie) {
            return;
        }

        $this->saveStatus = 'saving';

        $cart = $this->resolveCart($deviceCookie);

        $subtotal       = $cart ? (float) $cart->sub_total : 0;
        $deliveryCharge = 0;

        if ($this->delivery_area_id !== '') {
            $area           = delivery_areas::find($this->delivery_area_id);
            $deliveryCharge = $area ? (float) $area->charge : 0;
        }

        $total = $subtotal + $deliveryCharge;

        // Upsert draft header
        $draft = OrderDraft::updateOrCreate(
            ['id' => $this->draftId ?? 0],
            [
                'device_id'        => $deviceCookie,
                'name'             => $this->name ?: null,
                'phone'            => $this->phone ?: null,
                'email'            => $this->email ?: null,
                'address'          => $this->address ?: null,
                'delivery_area_id' => $this->delivery_area_id ?: null,
                'payment_method'   => $this->payment_method ?: 'cod',
                'notes'            => $this->notes ?: null,
                'subtotal'         => $subtotal,
                'delivery_charge'  => $deliveryCharge,
                'total'            => $total,
                'expires_at'       => now()->addDays(7),
            ]
        );

        $this->draftId = $draft->id;

        // Sync draft items from current cart (full replace)
        $draft->items()->delete();

        if ($cart && $cart->items->isNotEmpty()) {
            $items = [];
            foreach ($cart->items as $item) {
                $items[] = [
                    'draft_id'      => $draft->id,
                    'product_id'    => $item->product_id,
                    'product_name'  => $item->product?->name,
                    'product_image' => $item->product?->image,
                    'quantity'      => (int) $item->quantity,
                    'price'         => (float) $item->price,
                    'total'         => (float) $item->total,
                    'options'       => is_string($item->options)
                        ? $item->options
                        : json_encode($item->options),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
            OrderDraftItem::insert($items);
        }

        $this->subtotal       = $subtotal;
        $this->deliveryCharge = $deliveryCharge;
        $this->total          = $total;
        $this->saveStatus     = 'saved';
    }

    public function clearDraft(): void
    {
        if ($this->draftId) {
            OrderDraft::find($this->draftId)?->delete();
        }

        $this->draftId          = null;
        $this->name             = '';
        $this->phone            = '';
        $this->email            = '';
        $this->address          = '';
        $this->delivery_area_id = '';
        $this->payment_method   = 'cod';
        $this->notes            = '';
        $this->subtotal         = 0;
        $this->deliveryCharge   = 0;
        $this->total            = 0;
        $this->saveStatus       = null;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function resolveCart(string $deviceCookie): ?Cart
    {
        $device = Device::where('device_id', $deviceCookie)->first();

        if (! $device) {
            return null;
        }

        return Cart::with(['items.product'])
            ->whereNull('customer_id')
            ->where('device_id', $device->id)
            ->first();
    }

    public function render()
    {
        $deliveryAreas = delivery_areas::orderBy('id')->get();

        return view('livewire.website.order-draft-manager', [
            'deliveryAreas' => $deliveryAreas,
        ]);
    }
}
