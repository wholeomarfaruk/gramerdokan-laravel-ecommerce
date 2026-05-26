<?php

namespace App\Livewire\Website;

use App\CAPI\AddToCartEvent;
use App\Jobs\SendMetaCapiEventJob;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Device;
use App\Models\products;
use Livewire\Attributes\On;
use Livewire\Component;
use Ramsey\Collection\Set;

class CartManager extends Component
{
    public $cart;

    public function mount()
    {
        $this->cart = $this->getCart();
    }

    public function getCart()
    {
        $device = Device::where('device_id', request()->cookie('_sfdid'))->first();

        return Cart::firstOrCreate(
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

    if($productId){
        $product = products::find($productId);
        if (!$product) {
            $this->dispatch('notify', type: 'error', message: 'Product not found.');
            return;
        }
    }

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
           $item = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => 1,
                'price' => $product->discounted_price,
                'total' => $product->discounted_price,
            ]);
        }



        $this->updateCartTotals($cart);

        $itemCount = $this->cart->items->count();
        setcookie('_cart_count', $itemCount, time() + (86400 * 30), "/");
        $this->dispatch('cart-updated', cartcount: $itemCount);

        // Meta CAPI AddToCart Event
        $segment = $product->segments?->select('name')?->first() ? strtolower($product->segments->select('name')->first()['name']) : null;

        $contents = [];

        $contents[] = [
            'id' => $item->product_id,
            'quantity' => $item->quantity,
            'item_price' => $item->price,
        ];
        $ecommerce = [
            'currency' => 'BDT',
            'value' => $item->total,
            'delivery_category' => 'home_delivery',
            'contents' => $contents,
        ];
        $thisAddToCartEvent = new AddToCartEvent();
        $thisAddToCartEvent->push(
            null,
            currency: 'BDT',
            contentPrice: $item->total,
            contentId: $item->product_id,
            contentName: $product->name,
            contentType: 'product',
            contentCategory: $segment,

        );
            $thisAddToCartEvent->set('contents', $contents);
            $thisAddToCartEvent->set('ecommerce', $ecommerce);
        SendMetaCapiEventJob::dispatch($thisAddToCartEvent->serverPayload())->onQueue(env('META_CAPI_QUEUE', 'metacapi'));
        $browserEventPayload = $thisAddToCartEvent->browserEventPayload();
        $this->dispatch('add-to-cart-event', payload: $browserEventPayload);

        $this->dispatch('notify', type: 'success', message: 'Added to cart successfully.');

    }

    private function updateCartTotals($cart)
    {
        $cart->load('items.product');

        $subTotal = $cart->items->sum('total');

        $cart->update([
            'sub_total' => $subTotal,
            'total' => $subTotal + $cart->delivery_charge,
            'grand_total' => ($subTotal + $cart->delivery_charge) - $cart->discount,
        ]);

        $this->cart = $cart->fresh('items.product');
    }
    public function clearCart()
    {
        $this->cart->items()->delete();
        $this->updateCartTotals($this->cart);
        $itemCount = $this->cart->items->count();
        setcookie('_cart_count', $itemCount, time() + (86400 * 30), "/");
        $this->dispatch('cart-updated', cartcount: $itemCount);
    }
    public function removeItem($rowId)
    {
        $cartItem = CartItem::find($rowId);

        if (!$cartItem) {
            $this->dispatch('notify', type: 'error', message: 'Cart item not found.');
            return;
        }

        $cartItem->delete();
        $this->updateCartTotals($this->cart);
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
        $itemCount = $this->cart->items->count();
        setcookie('_cart_count', $itemCount, time() + (86400 * 30), "/");
        $this->dispatch('cart-updated', cartcount: $itemCount);
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
        return view('livewire.website.cart-manager');
    }
}
