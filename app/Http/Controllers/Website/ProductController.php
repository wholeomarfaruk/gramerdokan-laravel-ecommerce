<?php

namespace App\Http\Controllers\Website;

use App\CAPI\InitiateCheckOutEvent;
use App\CAPI\ViewItemEvent;
use App\Http\Controllers\Controller;
use App\Jobs\SendMetaCapiEventJob;
use App\Models\delivery_areas;
use App\Models\products;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function productShow(Request $request, $segment, $slug)
    {


        $product = products::where('slug', $slug)->first();
        if (!$product) {
            abort(404);
        }
        $product->increment('views');

        $deliveryAreas = delivery_areas::limit(5)->get();
        $products = products::where('status', 1)->where('id', '!=', $product->id)->inRandomOrder()->limit(8)->get();
        $segment = $product?->segments?->select('name')?->first() ? strtolower($product->segments->select('name')->first()['name']) : null;

        $contents = [];
        $contents[] = [
            'id' => $product->id,
            'quantity' => 1,
            'item_price' => $product->discounted_price
        ];

        $ecommerce = [
            'currency' => 'BDT',
            'value' => $product->discounted_price,
            'delivery_category' => 'home_delivery',
            'contents' => $contents,
        ];
        $capi = new ViewItemEvent();
        // return config('conversionapi.meta_pixel_id');
        $capi->push(
            null,
            currency: 'BDT',
            contentPrice: $product->discounted_price,
            contentId: $product->id,
            contentName: $product->name,
            contentType: 'product',
            contentCategory: $segment,
        );
        $capi->set('contents', $contents);
        $capi->set('ecommerce', $ecommerce);
        SendMetaCapiEventJob::dispatch($capi->serverPayload())->onQueue(env('META_CAPI_QUEUE', 'metacapi'));
        $viewItemEventPayload = $capi->browserEventPayload();

        //initiate checkout
        $IntiateCheckoutEvent = new InitiateCheckOutEvent();
        $shipping_cost = $deliveryAreas?->first()?->charge ?? 80.00;
        $IntiateCheckoutEvent->push(
             null,
            currency: 'BDT',
            contentPrice: $product->price,
            contentId: $product->id,
            contentName: $product->name,
            contentType: 'product',
            contentCategory: $segment,
            contents: $contents,
            ecommerce: $ecommerce,
            shipping_cost: $shipping_cost
        );
        $initiateCheckoutEventPayload = $IntiateCheckoutEvent->browserEventPayload();
        // return response()->json($capi->sendServerSide());
        return view('product-show', compact('product', 'deliveryAreas', 'products', 'segment', 'viewItemEventPayload', 'initiateCheckoutEventPayload'));
    }
}
