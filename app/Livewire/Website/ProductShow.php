<?php

namespace App\Livewire\Website;

use App\CAPI\InitiateCheckOutEvent;
use App\Jobs\SendMetaCapiEventJob;
use App\Models\products;
use Illuminate\Http\Request;
use Livewire\Attributes\Rule;
use Livewire\Component;

class ProductShow extends Component
{
    public $product;
    public $segment;
    public $deliveryAreas;
    public $products;
    public $name, $phone, $address, $size, $product_id, $quantity=1, $delivery_area;

    public function mount($product, $segment, $deliveryAreas)
    {
        $this->product = $product;
        if(!$product){
            abort(404);
        }
        $this->segment = $segment;
        $this->deliveryAreas = $deliveryAreas;
        $this->products = products::where('status', 1)->where('id', '!=', $product->id)->inRandomOrder()->limit(8)->get();
        $this->product_id = $product->id;
    }
    public function render()
    {
        return view('livewire.website.product-show');
    }
    public function Rules()
    {
        return [
            'name' => 'required|string|max:255|min:3',
            'phone' => 'required|string|min:11|max:11',
            'address' => 'required',
            'size' => 'nullable',
            'product_id' => 'required',
            'quantity' => 'required',
            'delivery_area' => 'required',
        ];
    }
    public function messages()
{
    return [
        'name.required' => 'Name is required.',
        'name.min' => 'Name must be at least 3 characters.',
        'name.max' => 'Name cannot exceed 255 characters.',

        'phone.required' => 'Phone number is required.',
        'phone.min' => 'Phone number must be exactly 11 digits.',
        'phone.max' => 'Phone number must be exactly 11 digits.',

        'address.required' => 'Address is required.',

        'product_id.required' => 'Please select a product.',

        'quantity.required' => 'Quantity is required.',
        'quantity.integer' => 'Quantity must be a number.',
        'quantity.min' => 'Quantity must be at least 1.',

        'delivery_area.required' => 'Delivery area is required.',
    ];
}
    public function submit(Request $request){
        
            $contents=[];
       
            $contents[]=[
                    'id'=>$this->product_id,
                    'quantity'=>$this->quantity,
                    'item_price'=>$this->product->discounted_price,

            ];
            $ecommerce=[
                'currency'=>'BDT',
                'value'=>$this->product->discounted_price,
                'delivery_category'=>'home_delivery',
                'contents'=>$contents,
            ];
      
        $checkoutEvent=new InitiateCheckOutEvent();
        
        $checkoutEvent->push(
            eventId: null,
            currency: 'BDT',
            contentPrice: $this->product->discounted_price,
            contentId: $this->product->id,
            contentName: $this->product->name,
            contentType: 'product',
            contentCategory: $this->segment,
            contents: $contents,
            ecommerce: $ecommerce
        );

        $broweserEventpayload=$checkoutEvent->browserEventPayload();
        SendMetaCapiEventJob::dispatch($checkoutEvent->serverPayload())->onQueue(env('META_CAPI_QUEUE', 'metacapi'));
        $this->dispatch('initiate-checkout', $broweserEventpayload);
    }

}
