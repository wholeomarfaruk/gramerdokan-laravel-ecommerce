<?php

namespace App\Livewire\Website;

use App\CAPI\AddToCartEvent;
use App\Jobs\SendMetaCapiEventJob;
use App\Models\Cart as CartModel;
use App\Models\CartItem;
use App\Models\Device;
use App\Models\products;
use Livewire\Attributes\On;
use Livewire\Component;

class Cart extends Component
{
    public $cart;

    public function mount()
    {
        $this->cart = $this->getCart();
    }

    private function getCart()
    {
        $device = Device::where('device_id', request()->cookie('_sfdid'))->first();

        return CartModel::firstOrCreate(
            [
                'customer_id' => null,
                'device_id' => $device?->id,
            ],
            []
        );
    }

    #[On('add-to-cart')]
    public function addToCart($productId)
    {


        $product = products::findOrFail($productId);

        $cart = $this->getCart();

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();

        if ($item) {
            $item->quantity += 1;
            $item->total = $item->quantity * $item->price;
            $item->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => 1,
                'price' => $product->price,
                'total' => $product->price,
            ]);
        }

        $this->updateCartTotals($cart);

        $this->dispatch('notify', type: 'success', message: 'Added to cart successfully.');
        $itemCount = $this->cart->items->count();
        setcookie('_cart_count', $itemCount, time() + (86400 * 30), "/");
        $this->dispatch('cart-updated', cartcount: $itemCount);

    }

    private function updateCartTotals($cart)
    {
        $cart->load('items.product');

        $subTotal = $cart->items->sum('total');

        $cart->update([
            'sub_total' => $subTotal,
            'total' => $subTotal,
            'grand_total' => $subTotal + $cart->delivery_charge + $cart->tax - $cart->discount,
        ]);

        $this->cart = $cart->fresh('items.product');


    }
    public function clearCart()
    {
        $this->cart->items()->delete();
        $this->updateCartTotals($this->cart);
        $this->dispatch('notify', type: 'success', message: 'Cart cleared successfully.');
        $itemCount = $this->cart->items->count();
        setcookie('_cart_count', $itemCount, time() + (86400 * 30), "/");
        $this->dispatch('cart-updated', cartcount: $itemCount);
    }
    public function removeItem($rowId)
    {
        CartItem::find($rowId)->delete();
        $this->updateCartTotals($this->cart);

        $this->dispatch('notify', type: 'success', message: 'Item removed from cart successfully.');
        $itemCount = $this->cart->items->count();
        setcookie('_cart_count', $itemCount, time() + (86400 * 30), "/");
        $this->dispatch('cart-updated', cartcount: $itemCount);
    }
    public function increaseQty($itemId)
    {
        $item = CartItem::findOrFail($itemId);

        $item->quantity += 1;
        $item->total = $item->quantity * $item->price;
        $item->save();

        $this->updateCartTotals($item->cart);

    }

    public function decreaseQty($itemId)
    {
        $item = CartItem::findOrFail($itemId);

        if ($item->quantity > 1) {
            $item->quantity -= 1;
            $item->total = $item->quantity * $item->price;
            $item->save();
        } else {
            $item->delete(); // quantity 1 হলে remove
        }

        $this->updateCartTotals($item->cart);
    }
    public function render()
    {

        $contents = [];

        foreach ($this->cart->items as $item) {
            $contents[] = [
                'id' => $item->product_id,
                'quantity' => $item->quantity,
                'item_price' => $item->price,
            ];
        }
        $ecommerce = [
            'currency' => 'BDT',
            'value' => $this->cart->sub_total,
            'delivery_category' => 'home_delivery',
            'contents' => $contents,
        ];
        $AddToCartEvent = new AddToCartEvent();
        $AddToCartEvent->push(
            eventId: null,
            currency: null,
            contentPrice: $this->cart->sub_total,
            contentId: null,
            contentName: null,
            contentType: 'product',
            contentCategory: null,

        );
        $AddToCartEvent->set('content_ids', $this->cart->items->pluck('product_id')->toArray());
        $AddToCartEvent->set('contents', $contents);
        $AddToCartEvent->set('ecommerce', $ecommerce);
        $serverPayload = $AddToCartEvent->serverPayload();
        $AddToCartbrowserPayload = $AddToCartEvent->browserEventPayload();
        SendMetaCapiEventJob::dispatch($serverPayload)->onQueue(env('META_CAPI_QUEUE', 'metacapi'));

        return view('livewire.website.cart', compact('AddToCartbrowserPayload'));
    }
}
