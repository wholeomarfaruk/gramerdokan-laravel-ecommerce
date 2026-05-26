<?php

namespace App\Livewire\Website;

use App\CAPI\InitiateCheckOutEvent;
use App\Http\Controllers\HomeController;
use App\Jobs\SendMetaCapiEventJob;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\delivery_areas;
use App\Models\Device;
use App\Models\Order;
use App\Models\Order_Item;
use App\Models\OrderDraft;
use App\Models\products;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Intervention\Image\Colors\Rgb\Channels\Red;
use Livewire\Component;

class Checkout extends Component
{
    public $cartData;
    public $name, $phone, $address, $note, $delivery_area, $payment_method = 'cod', $transaction_id, $delivery_charge, $discount;

    public function mount()
    {
        $this->cartData = $this->getCart();

        if (! $this->cartData || $this->cartData->items->isEmpty()) {
            session()->flash('cart_error', 'Your cart is empty. Add some products before checkout.');
            $this->redirectRoute('cart.view', navigate: true);
            return;
        }
        $this->delivery_area = delivery_areas::orderBy('id', 'asc')->get()->first()->id;
        $this->delivery_charge = delivery_areas::find($this->delivery_area)->charge;
        $this->cartData->delivery_charge = $this->delivery_charge;
        $this->calculateTotal();
        $this->cartData = $this->getCart();
    }
    public function calculateTotal()
    {
        $subTotal = $this->cartData->items->sum(function ($item) {
            return (float) $item->price * (int) $item->quantity;
        });

        $delivery = (float) ($this->delivery_charge ?? $this->cartData->delivery_charge ?? 0);
        $discount = (float) ($this->discount ?? $this->cartData->discount ?? 0);

        if ($discount < 0) {
            $discount = 0;
        }

        $total = $subTotal + $delivery;
        $grandTotal = $total - $discount;

        if ($grandTotal < 0) {
            $grandTotal = 0;
        }

        $this->cartData->update([
            'delivery_charge' => $delivery,
            'discount' => $discount,
            'sub_total' => $subTotal,
            'total' => $total,
            'grand_total' => $grandTotal,
        ]);

        $this->cartData->refresh();
    }


    private function getCart()
    {
       $device = Device::where('device_id', request()->cookie('_sfdid'))->first();

        if (! $device) {
            return null;
        }

        return Cart::with(['items.product'])
            ->where('customer_id', null)
            ->where('device_id', $device->id)
            ->first();
    }
    public function place_order(Request $request)
    {

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'delivery_area' => ['nullable', 'exists:delivery_areas,id'],
            'payment_method' => ['nullable', 'string'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
        ]);

        if (! $this->cartData || $this->cartData->items->isEmpty()) {
            return redirect()->route('cart.view')->with([
                'status' => 'error',
                'message' => 'Your cart is empty.',
            ]);
        }

        $phone = preg_replace('/\D/', '', $this->phone);

        if (str_starts_with($phone, '88') && strlen($phone) > 11) {
            $phone = substr($phone, 2);
        }

        $deliveryArea = delivery_areas::findOrFail($this->delivery_area);

        try {
            \DB::transaction(function () use ($request, $phone, $deliveryArea, &$order) {
                $order = new Order();
                $order->name = $this->name;
                $order->phone = $this->phone;
                $order->address = $this->address;
                $order->delivery_area_id = $deliveryArea->id;
                $order->cod_percentage = 0;
                $order->cod_charge = 0;
                $order->subtotal = $this->cartData->subTotal() ?? 0;
                $order->total = $this->cartData->total ?? 0;
                $order->discount = $this->cartData->discount ?? 0;
                $order->fee = $deliveryArea->charge ?? 0;
                $order->payment_method = $this->payment_method;
                if ($this->payment_method == 'cod') {
                    $order->payment_status = 'unpaid';
                } elseif ($this->payment_method == 'bKash' && $this->transaction_id != null) {
                    $order->payment_status = 'paid';
                    $order->is_paid = true;
                }
                $order->transaction_id = $this->transaction_id;
                $order->notes = $this->note;
                $order->status = 'pending';
                $order->ip_address = $request->ip();
                $order->user_agent = $request->userAgent();
                $order->json_data = [
                    'order_data' => $request->except(['_token', 'XSRF_TOKEN']),
                    'cart' => $this->cartData->toArray(),
                ];
                $order->save();

                foreach ($this->cartData->items as $item) {
                    $product = $item->product;

                    if ($product->quantity < $item->quantity) {
                        throw new \Exception("Insufficient stock for {$product->name}");
                    }

                    Order_Item::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'total' => $item->total,
                        'options' => $item->options,
                        'product_name' => $product->name,
                        'product_image' => $product->image,
                    ]);

                    $product->quantity -= $item->quantity;
                    if ($product->quantity <= 0) {
                        $product->stock_status = 'out_of_stock';
                    }
                    $product->save();
                }

                $customer = Customer::firstOrCreate(
                    ['phone' => $phone],
                    [
                        'first_name' => $this->name,
                        'address' => $this->address,
                    ]
                );

                $customer->orders()->syncWithoutDetaching([$order->id]);
                $this->cartData->delete();

                $deviceId = request()->cookie('_sfdid');
                if ($deviceId) {
                    OrderDraft::where('device_id', $deviceId)->delete();
                }
            });

            return redirect()->route('order.received', ['order' => $order->id]);
        } catch (\Throwable $th) {
            \Log::error($th);

            dd($th->getMessage());
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Order place failed. Please try again.',
            ]);
        }
    }
    public function updatedDeliveryArea($areaId)
    {

        $this->delivery_area = $areaId;
        $this->delivery_charge = delivery_areas::find($areaId)->charge;
        $this->calculateTotal();
    }
    public function render()
    {
        $bkashNumber = '01682963493';
        $deliveryAreas = delivery_areas::orderBy('id', 'asc')->get();

        //initiate checkout

        $contents = [];

        foreach ($this->cartData->items as $item) {
            $contents[] = [
                'id' => $item->product_id,
                'quantity' => $item->quantity,
                'item_price' => $item->price,
            ];
        }
        $ecommerce = [
            'currency' => 'BDT',
            'value' => $this->cartData->sub_total,
            'delivery_category' => 'home_delivery',
            'contents' => $contents,
        ];
        $IntiateCheckoutEvent = new InitiateCheckOutEvent();
        $shipping_cost = $deliveryAreas?->first()?->charge ?? 80.00;
        $IntiateCheckoutEvent->push(
             null,
            currency: 'BDT',
            contentPrice: $this->cartData->grand_total,
            contentId: null,
            contentName: null,
            contentType: 'product',
            contentCategory: null,
            contents: $contents,
            ecommerce: $ecommerce,
            shipping_cost: $shipping_cost
        );
        $IntiateCheckoutEvent->set('content_ids', $this->cartData->items->pluck('product_id')->toArray());
         SendMetaCapiEventJob::dispatch($IntiateCheckoutEvent->serverPayload())->onQueue(env('META_CAPI_QUEUE', 'metacapi'));
        $initiateCheckoutEventPayload = $IntiateCheckoutEvent->browserEventPayload();

        return view('livewire.website.checkout', [
            'cartData' => $this->cartData,
            'bkashNumber' => $bkashNumber,
            'deliveryAreas' => $deliveryAreas,
            'initiateCheckoutEventPayload' => $initiateCheckoutEventPayload,
        ]);
    }
}
