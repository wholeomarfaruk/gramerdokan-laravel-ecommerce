<?php

namespace App\Http\Controllers;

use App\CAPI\PurchaseEvent;
use App\Helper\Convert;
use App\Jobs\SendMetaCapiEventJob;
use App\Livewire\Website\CartManager;
use App\Models\Cart as ModelsCart;
use App\Models\Device;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use App\Models\products;
use App\Models\delivery_areas;
use App\Models\Order;
use App\Models\Order_Item;
use App\Models\OrderDraft;
use App\Models\OrderDraftItem;
use App\Models\Coupon;
use Esign\ConversionsApi\Facades\ConversionsApi;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;
use Validator;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class CartController extends Controller
{
    protected $pixelId = "2050688985371395";
    protected $accessToken = "EAAWH3RG69LkBPTTsgdYyicYY7zzckWU71l0O9vzHcisQtQzSwpwYK546Cp5v96FY0Kx2f6mgWT3DoSA8rPdpnIZCL6nwnxlKHXdQiyZBDZBof11syObER1nXZC2d6S6wV0Jy6O9DMYPV8W4hkY6ryEu07f807iQVJuxnlWWmor6YqVdlnCL2pODZB18iL8SSoogZDZD";

    protected $client;
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }
    public function test()
    {


        return response()->json([
            'data' => Cart::instance('cart')->content(),
        ]);

    }
    public function cart_calculate($delivery_charge = 0, $cod_charge_percent = 0)
    {

        $items = Cart::instance('cart')->content();
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item->price * $item->qty;
        }
        $discount = 0;
        if (Session::has('coupon')) {
            if (Session::get('coupon')['type'] == 'fixed') {
                $discount = Session::get('coupon')['value'];
            } else {
                $discount = ($subtotal * Session::get('coupon')['value']) / 100;
            }
        }


        $total = $subtotal - $discount + $delivery_charge;
        $cod_charge = ($cod_charge_percent > 0) ? ($total * $cod_charge_percent / 100) : 0;
        $total = $total + $cod_charge;
        Session::put('mycart', [
            'total' => $total,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'delivery_charge' => $delivery_charge,
            'cod_charge' => $cod_charge,
            'cod_charge_percent' => $cod_charge_percent
        ]);
        return [
            'mycart' => [
                'total' => $total,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'delivery_charge' => $delivery_charge,
                'cod_charge' => $cod_charge,
                'cod_charge_percent' => $cod_charge_percent
            ]
        ];

    }

    public function add_to_cart(Request $request)
    {
        $product = products::find($request->id);

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }
        // Debug the sale_price

        if ($product->sale_price == null) {
            $product->sale_price = $product->regular_price;
        }
        Cart::instance('cart')->add($product->id, $product->name, $request->quantity, $product->sale_price)->associate('App\Models\Product');

        return redirect()->back()->with('status', 'Product Added To Cart');
    }

    public function cart_distroy()
    {
        Cart::instance('cart')->destroy();
        return "cart distried successfully";
    }


    public function add_json_to_cart(Request $request)
    {

        // Your existing code...
        $validated = $request->validate([
            'cartItems' => 'required|array',
            'cartItems.*.id' => 'required|integer',
            'cartItems.*.name' => 'required|string',
            'cartItems.*.weight' => 'required',
            'cartItems.*.quantity' => 'required|integer',
            'cartItems.*.price' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'delivery_charge' => 'required|numeric',
            'total' => 'required|numeric',
            'cod' => 'required|numeric',
        ]);




        $cartIcontent = Cart::instance('cart')->content();
        // Remove all items from the cart that are not in the JSON data.
        foreach ($cartIcontent as $item) {
            $found = false;

            // Check if the item is in the JSON data.
            foreach ($validated['cartItems'] as $cartItem) {
                if ($item->id == $cartItem['id']) {
                    $found = true;
                    break;
                }
            }
            // If the item is not found in the JSON data, remove it from the cart.
            if (!$found) {
                Cart::instance('cart')->remove($item->rowId);
            }

        }

        // Add the items from the JSON data to the cart.
        foreach ($validated['cartItems'] as $item) {
            $product = products::find($item['id']);
            $cart = Cart::instance('cart');
            $cartItem = $cart->search(function ($cartItem) use ($item) {
                return $cartItem->id === $item['id'];
            })->first();

            if ($cartItem) {
                $cart->update($cartItem->rowId, $item['quantity']);
            } else {
                Cart::instance('cart')->add($item['id'], $item['name'], $item['quantity'], $product->price, [], "0")->associate('App\Models\products');
            }
        }

        $this->cart_calculate($validated['delivery_charge'], $validated['cod']);


        $cartValue = Session::get('mycart');
        return response()->json([
            'success' => true,
            'message' => 'Items added to cart successfully!',
            'total' => $cartValue['total'],
            'subtotal' => $cartValue['subtotal'],
            'delivery_charge' => $cartValue['delivery_charge'],
            'cod_charge' => $cartValue['cod_charge'],
            'cod_charge_percent' => $cartValue['cod_charge_percent'],
            'discount' => $cartValue['discount'],
            'data' => Cart::instance('cart')->content(),
        ]);
    }



    public function remove_item(Request $request)
    {
        Cart::instance('cart')->remove($request->id);
        return redirect()->back();
    }

    public function increase_quantity($rowId)
    {

        $item = Cart::instance('cart')->get($rowId);
        Cart::instance('cart')->update($rowId, $item->qty + 1);
        return redirect()->back();
    }

    public function decrease_quantity($rowId)
    {
        $item = Cart::instance('cart')->get($rowId);
        Cart::instance('cart')->update($rowId, $item->qty - 1);
        return redirect()->back();
    }

    public function clear_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function calculate_discount()
    {
        $discount = 0;

        if (Session::has('coupon')) {
            // Ensure subtotal is converted to a proper numeric format
            $subtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));

            // Determine discount based on coupon type
            if (Session::get('coupon')['type'] == 'fixed') {
                $discount = Session::get('coupon')['value'];
            } else {
                $discount = ($subtotal * Session::get('coupon')['value']) / 100;
            }

            // Calculate subtotal after discount
            $subtotalAfterDiscount = $subtotal - $discount;

            // Update session with discounts, keeping values as floats
            Session::put('discounts', [
                'discount' => number_format($discount, 2),  // Rounded for precision
                'subtotal' => number_format($subtotalAfterDiscount, 2),
                'total' => round($subtotalAfterDiscount, 2),
            ]);
        }
    }

    public function apply_coupon(Request $request)
    {
        if (isset($request->coupon_code)) {
            $coupon = Coupon::where('code', $request->coupon_code)->where('expiry_date', '>=', Carbon::now()->format('Y-m-d'))->first();
            if ($coupon) {
                Session::put('coupon', [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'cart_value' => $coupon->cart_value,
                    'expiry_date' => $coupon->expiry_date
                ]);

                $this->calculate_discount();
                return redirect()->back()->with('coupon_status', 'Coupon Applied Successfully');
            } else {
                return redirect()->back()->with('coupon_error', 'Invalid Coupon Code');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid Coupon Code');
        }


    }
    public function remove_coupon()
    {
        Session::forget('coupon');
        Session::forget('discounts');
        return redirect()->back()->with('coupon_status', 'Coupon Removed Successfully');
    }

    public function place_order(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'delivery_area' => 'required',
        ]);
        $phone = preg_replace('/\D/', '', $request->phone);
        if (str_starts_with($phone, '88') && strlen($phone) > 11) {
            $phone = substr($phone, 2);
        }
        if (str_starts_with($phone, '0') && strlen($phone) == 10) {
            $phone = '0' . $phone;
        }

        $extra_data = [];
        $extra_data['order_data'] = $request->all();
        $check_recent_order = Order::where('phone', $phone)
            ->where('status', 'pending')
            ->latest('created_at') // Get most recent order
            ->first();

        $diffInMinutes = 0;

        if ($check_recent_order) {
            // Find if that order has the same product
            $product_found = $check_recent_order->Order_Item()
                ->where('product_id', $request->product_id)
                ->latest('created_at')
                ->first();

            if ($product_found) {
                $createdAt = Carbon::parse($product_found->created_at);
                $now = Carbon::now();

                $diffInMinutes = $createdAt->diffInMinutes($now);

                if ($diffInMinutes < 2) {
                    return redirect()->back()->with([
                        'status' => 'error',
                        'message' => 'আপনি এই প্রডাক্টি অলরেডি অর্ডার করেছেন। দয়া করে ২ মিনিট পর পুনরায় করুন। অথবা 01622351266 ওয়াটসঅ্যাপ এ যোগাযোগ করুন।',
                    ]);

                }
            }
        }
        $segment = null;
        try {
            //code...

            $product = products::find($request->product_id);
            $deliveryArea = delivery_areas::find($request->delivery_area);
            $deliveryCharge = $deliveryArea->charge;

            // Convert price and delivery charge to float for calculation
            $price = (float) ($product->discount_price ?? $product->price);
            $quantity = (float) $request->quantity;
            $delivery = (float) $deliveryCharge;

            // Calculate total
            $total = ($price * $quantity) + $delivery;
            $order = new Order();
            $order->name = $request->name;
            $order->phone = $phone ?? $request->phone;
            $order->address = $request->address;
            $order->delivery_area_id = $deliveryArea->id ?? null;
            $order->cod_percentage = '0';
            $order->cod_charge = '0';
            $order->subtotal = $total;
            $order->total = $total ?? '0';
            $order->discount = '0';
            $order->fee = $deliveryArea->charge ?? 0;

            $order->is_paid = false;
            $order->payment_status='unpaid';
            $order->status = 'pending';
            if ($request->server('REMOTE_ADDR')) {
                $order->ip_address = $request->server('REMOTE_ADDR');
            }

            if ($request->server('HTTP_USER_AGENT')) {
                $order->user_agent = $request->server('HTTP_USER_AGENT');
            }

            if ($extra_data) {
                $order->json_data = $extra_data;
            }
            $order->save();

            $orderItem = new Order_Item();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $request->product_id;
            $orderItem->price = $product->discount_price ?? $product->price;
            $orderItem->quantity = $request->quantity;
            if ($request->has('size')) {

                $orderItem->options = ['size' => $request->size ?? ''];
            }

            $orderItem->save();

            $product->quantity = (float) $product->quantity - (float) $request->quantity;
            if ($product->quantity <= 0) {
                $product->stock_status = 'out_of_stock';
            }
            $product->save();

            $customer = Customer::where('phone', $request->phone)->first();
            if (!$customer) {
                $customer = new Customer();
                $customer->first_name = $request->name;
                $customer->phone = $request->phone;
                $customer->address = $request->address;
                $customer->save();
                HomeController::updateCustomerAddress($customer->id, $request->address);
            }
            $customer->orders()->attach($order->id);
            $order_received_url = route('order.received', ['order' => $order->id]);
            $segment_name = $product?->segments?->first()?->name ?? null;
            if (!$segment_name) {
                $segment_name = strtolower($segment_name);
            }
            $order->trackingEvent()->create([
                'is_fired' => false,
                'tud_id' => $_COOKIE['_fbp'] ?? null,
                'tracking_id' => $_COOKIE['custom_fbc'] ?? null,
                'event_name' => 'Purchase',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'referrer' => $request->header('Referer'),
                'url' => $order_received_url,
                'segment' => $segment_name,
            ]);
            $deviceId = $request->cookie('_sfdid');
            if ($deviceId) {
                OrderDraft::where('device_id', $deviceId)->delete();
            }
            return redirect()->route('order.received', ['order' => $order->id]);
        } catch (\Throwable $th) {
            //throw $th;
            return $th->getMessage();
        }
    }
    public function orderAutosave(Request $request)
    {
        $request->validate(['phone' => 'required']);

        $phone = preg_replace('/\D/', '', $request->phone);
        if (str_starts_with($phone, '88') && strlen($phone) > 11) {
            $phone = substr($phone, 2);
        }
        if (str_starts_with($phone, '0') && strlen($phone) == 10) {
            $phone = '0' . $phone;
        }

        try {
            $product      = products::find($request->product_id);
            $deliveryArea = delivery_areas::find($request->delivery_area);
            $deliveryCharge = (float) ($deliveryArea->charge ?? 0);

            $price    = (float) ($product->discount_price ?? $product->price ?? 0);
            $quantity = (int)   ($request->quantity ?? 1);
            $subtotal = $price * $quantity;
            $total    = $subtotal + $deliveryCharge;

            $deviceId = $request->cookie('_sfdid');

            $draft = OrderDraft::updateOrCreate(
                ['device_id' => $deviceId],
                [
                    'name'             => $request->name,
                    'phone'            => $phone,
                    'address'          => $request->address,
                    'delivery_area_id' => $deliveryArea->id ?? null,
                    'payment_method'   => 'cod',
                    'subtotal'         => $subtotal,
                    'delivery_charge'  => $deliveryCharge,
                    'discount'         => 0,
                    'total'            => $total,
                    'expires_at'       => now()->addDays(7),
                ]
            );

            // Replace items for this draft
            $draft->items()->delete();

            $options = $request->has('size') && $request->size
                ? ['size' => $request->size]
                : null;

            OrderDraftItem::create([
                'draft_id'      => $draft->id,
                'product_id'    => $product->id ?? null,
                'product_name'  => $product->name ?? null,
                'product_image' => $product->image ?? null,
                'quantity'      => $quantity,
                'price'         => $price,
                'total'         => $price * $quantity,
                'options'       => $options ?: null,
            ]);

            return response()->json([
                'success'  => true,
                'message'  => 'Draft saved.',
                'draft_id' => $draft->id,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }
    public function orderAutosaveCheckout(Request $request)
    {
        $request->validate(['phone' => 'required']);

        $phone = preg_replace('/\D/', '', $request->phone);
        if (str_starts_with($phone, '88') && strlen($phone) > 11) {
            $phone = substr($phone, 2);
        }

        try {
            $deviceId = $request->cookie('_sfdid');

            $device = Device::where('device_id', $deviceId)->first();
            if (!$device) {
                return response()->json(['success' => false, 'message' => 'Device not found'], 422);
            }

            $cart = ModelsCart::with(['items.product'])
                ->where('device_id', $device->id)
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Cart is empty'], 422);
            }

            $deliveryArea   = delivery_areas::find($request->delivery_area_id);
            $deliveryCharge = (float) ($deliveryArea?->charge ?? 0);
            $subtotal       = $cart->items->sum(fn($i) => (float)$i->price * (int)$i->quantity);
            $total          = $subtotal + $deliveryCharge;

            $draft = OrderDraft::updateOrCreate(
                ['device_id' => $deviceId],
                [
                    'name'             => $request->name,
                    'phone'            => $phone,
                    'address'          => $request->address,
                    'delivery_area_id' => $deliveryArea?->id,
                    'payment_method'   => $request->payment_method ?? 'cod',
                    'subtotal'         => $subtotal,
                    'delivery_charge'  => $deliveryCharge,
                    'discount'         => 0,
                    'total'            => $total,
                    'notes'            => $request->note,
                    'expires_at'       => now()->addDays(7),
                ]
            );

            $draft->items()->delete();

            $now   = now();
            $rows  = $cart->items->map(fn($item) => [
                'draft_id'      => $draft->id,
                'product_id'    => $item->product_id,
                'product_name'  => $item->product?->name,
                'product_image' => $item->product?->image,
                'quantity'      => $item->quantity,
                'price'         => $item->price,
                'total'         => $item->total,
                'options'       => null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ])->toArray();

            OrderDraftItem::insert($rows);

            return response()->json(['success' => true, 'draft_id' => $draft->id]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function order_received(Request $request)
    {

        $order = Order::find($request->order);
        if (!$order) {
            return redirect()->route('home.index');
        }
        $orderItems = Order_Item::where('order_id', $order->id)->get();
        $subtotal = 0;
        $orderItems->transform(function ($item) {
            $item->subtotal = $item->price * $item->quantity;
            return $item;
        });
$segment = optional($order->Order_Item()->first()?->product?->segments()->first())->name;

        $customer = $order->customer->first();

        $eventId = $order->id;

        $contents=[];
        foreach ($orderItems as $key => $value) {
            $contents[]=[
                    'id'=>$value->product_id,
                    'quantity'=>$value->quantity,
                    'item_price'=>$value->price,

            ];
        }


            $ecommerce = [
            'currency' => 'BDT',
            'transaction_id' => $order->id,
            'value' => $order->total,
            'delivery_category' => 'home_delivery',
            'contents' => $contents,
            'num_items' => count($orderItems),
        ];
        $content_ids = $orderItems->pluck('product_id')->toArray() ?? null;

        $capi = new PurchaseEvent();
        $capi->push(
            eventId:$eventId,
            currency: 'BDT',
            contentPrice: $order->total,
            contentIds: $content_ids,
            content_type: 'product',
            contents: $contents,
            ecommerce: $ecommerce,
            shipping_cost: $order->fee,
            order_id: $order->id,
        );


        SendMetaCapiEventJob::dispatch($capi->serverPayload())->onQueue(env('META_CAPI_QUEUE', 'metacapi'));
        $purchaseEventPayload = $capi->browserEventPayload();
        // if($response){

        //     $order->trackingEvent->update([
        //     'is_fired' => $response['ok'],
        //     'json_data' => $response,
        //     'event_fired_time' => now(),
        // ]);
        // }

        return view('order-received', compact('order', 'orderItems', 'customer', 'segment', 'purchaseEventPayload'));
    }
    public function order_received_custom($id)
    {

        $order = Order::find($id);
        if (!$order) {
            $errors = ['status' => 'Order not found'];
            return view('templates.error', compact('errors'));
        }
        $trackingEvent = $order->trackingEvent;
        if ($order->isEventFired == true) {
            $errors = ['event_status' => $order->eventStatus];
            return view('templates.error', compact('errors'));
            // return view('order-received-custom', compact('order'))->with('event_status', $order->eventStatus);
        }

        $orderItems = Order_Item::where('order_id', $order->id)->get();

        if ($orderItems->isEmpty()) {
            $errors = ['event_status' => 'Item not found'];
            return view('templates.error', compact('errors'));

            // return view('order-received-custom', compact('order'))->with('event_status', 'Item not found');
        }
        $subtotal = 0;
        $orderItems->transform(function ($item) {
            $item->subtotal = $item->price * $item->quantity;
            return $item;
        });
        $customer = $order->customer->first();

        // Customer info (hash করতে হবে)
        $userData = [
            'client_user_agent' => $trackingEvent->user_agent,
            'client_ip_address' => $trackingEvent->ip_address,
            'fbp' => $trackingEvent->tud_id ?? null,
            'fbc' => $trackingEvent->tracking_id ?? null,

        ];

        $filterData = ['men', 'women', 'kids'];
        $segment = $trackingEvent->segment;
        $segment2 = $segment;
        $pixel_id = config('static_data.meta.segment.default.pixel_id');
        $pixel_access_token = config('static_data.meta.segment.default.access_token');
        $pixel_test_event_code = config('static_data.meta.segment.default.test_event_code');
        if ($trackingEvent->segment) {
            $segment = strtolower($trackingEvent->segment);

            foreach ($filterData as $value) {
                if (preg_match('/\b' . preg_quote($value, '/') . '\b/', $segment)) { // case-insensitive search, match full exact and full word
                    $segment = $value;
                    break; // stop at first match
                }
            }
            $segment3 = $segment;
            if ($segment == 'men') {
                $pixel_id = config('static_data.meta.segment.men.pixel_id');
                $pixel_access_token = config('static_data.meta.segment.men.access_token');
                $pixel_test_event_code = config('static_data.meta.segment.men.test_event_code');
            } elseif ($segment == 'women') {
                $pixel_id = config('static_data.meta.segment.women.pixel_id');
                $pixel_access_token = config('static_data.meta.segment.women.access_token');
                $pixel_test_event_code = config('static_data.meta.segment.women.test_event_code');
            }

        }
        $test_event = false;

        if ($test_event) {
            $pixel_id = config('static_data.meta.segment.test.pixel_id');
            $pixel_access_token = config('static_data.meta.segment.test.access_token');
            $pixel_test_event_code = config('static_data.meta.segment.test.test_event_code');
        }

        $contents = [];

        foreach ($orderItems as $item) {
            $contents[] = [
                'id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => floatval($item->price),
            ];
        }

        // Purchase data
        $customData = [
            'currency' => 'BDT',
            'value' => $order->total,
            'contents' => $contents,
            'content_ids' => [$orderItems->first()->product->id],
            'content_type' => 'product',
        ];


        $eventId = $order->id;

        $payload = [
            'data' => [
                [
                    'event_name' => 'Purchase',
                    'action_source' => 'website',
                    'event_time' => $order->created_at->timestamp,
                    'event_id' => (string) $eventId ?? (string) Str::uuid(),
                    'event_source_url' => $trackingEvent->url ?? null,
                    'referrer_url' => $trackingEvent->referrer ?? null,

                    'custom_data' => [
                        'currency' => 'BDT',
                        'value' => floatval($order->total) ?? null,
                        'transaction_id' => $order->id ?? null,
                        'contents' => $contents,
                    ],
                    'user_data' => [
                        'client_user_agent' => $trackingEvent->user_agent ?? null,
                        'client_ip_address' => $trackingEvent->ip_address ?? null,
                        'fbp' => isset($trackingEvent->tud_id) ? $trackingEvent->tud_id : null,
                        'fbc' => isset($trackingEvent->tracking_id) ? $trackingEvent->tracking_id : null,
                        'ph' => isset($customer->phone) ? Convert::normalizeAndHash($customer->phone) : null,
                        'fn' => isset($customer->first_name) ? Convert::normalizeAndHash(strtolower(trim($customer->first_name))) : null,
                        'ln' => isset($customer->last_name) ? Convert::normalizeAndHash(strtolower(trim($customer->last_name))) : null,
                        'external_id' => isset($customer?->id) ? Convert::normalizeAndHash(strtolower(trim($customer->id))) : null,
                        'country' => Convert::normalizeAndHash('bd'),
                        'ge' => $customer?->gender ? Convert::normalizeAndHash($customer?->gender) : null,
                        'st' => $customer?->state ? Convert::normalizeAndHash($customer?->state) : null,
                        'ct' => $customer?->city ? Convert::normalizeAndHash($customer?->city) : null,
                        'zp' => $customer?->zip_code ? Convert::normalizeAndHash($customer?->zip_code) : null,
                    ],
                ],
            ],
            // 'test_event_code' => $pixel_test_event_code,
        ];


        $payload = Convert::cleanArray($payload);
        // return response()->json($payload
        //     [
        //         'status' => 'success',
        //         'data' => $payload,
        //         'pixel_id' => $pixel_id,
        //         'pixel_access_token' => $pixel_access_token,
        //         'test_event_code' => $pixel_test_event_code,
        //         'segment' => $trackingEvent->segment,
        //         'segement2' => $segment2,
        //         'segment3' => $segment3,
        //     ]
        // );
        $client = new Client([
            'base_uri' => 'https://graph.facebook.com/v23.0/',
        ]);
        $response = $client->post($pixel_id . '/events', [
            'query' => [
                'access_token' => $pixel_access_token,
            ],
            'json' => $payload,
        ]);
        $trackingEvent->update([
            'is_fired' => true,
            'json_data' => $response->getBody()->getContents(),
            'event_fired_time' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'response' => $response->getBody()->getContents(),
            'data' => $payload,
            'segment' => $segment,
            'pixel_id' => $pixel_id,
        ]);
        $successes = [];
        $errors = [];
        $payload = json_encode($payload, JSON_PRETTY_PRINT);
        $segment = $orderItems?->first()?->product->segments()?->select('name')?->first() ? strtolower($orderItems->first()->product->segments->select('name')->first()['name']) : null;
        return view('order-received-custom', compact('order', 'orderItems', 'customer', 'segment', 'trackingEvent', 'pixel_id', 'pixel_access_token', 'pixel_test_event_code', 'contents', 'userData', 'customData', 'eventId', 'payload', 'successes', 'errors'));
    }
    public function orderNow(Request $request)
    {
        $response = [];
        if (!$request->phone || $request->phone == '') {
            return response()->json([
                'status' => 'error',
                'is_show' => true,
                'message' => 'আপনার ফোন নাম্বারের ঘর খালি রয়েছে। আপনার ফোন নাম্বার দিয়ে অর্ডার করুন',
            ]);
        } elseif (!is_numeric($request->phone)) {

            return response()->json([
                'status' => 'error',
                'is_show' => true,
                'message' => 'আপনার ফোন নাম্বার একটি সংখ্যা হতে হবে',
            ]);
        } elseif (strlen($request->phone) != 11) {

            return response()->json([
                'status' => 'error',
                'is_show' => true,
                'message' => 'নাম্বার চেক করুন, আপনার নাম্বার ১১ ডিজিট হতে হবে',
            ]);
        } elseif ($request->address == '' || $request->address == null) {

            return response()->json([
                'status' => 'error',
                'is_show' => true,
                'message' => 'আপনার ঠিকানা দিয়ে অর্ডার করুন',
            ]);
        }

        $phone = preg_replace('/\D/', '', $request->phone);
        if (str_starts_with($phone, '88') && strlen($phone) > 11) {
            $phone = substr($phone, 2);
        }
        if (strlen($phone) != 11) {
            return response()->json([
                'status' => 'error',
                'is_show' => true,
                'message' => 'নাম্বার চেক করুন, আপনার নাম্বার ১১ ডিজিট হতে হবে',
            ]);
        }


        $extra_data = [];
        $extra_data['order_data'] = $request->except(['_token', 'XSRF_TOKEN']);
        $check_recent_order = Order::where('phone', $phone)
            ->where('status', 'pending')
            ->latest('created_at') // Get most recent order
            ->first();

        $diffInMinutes = 0;

        if ($check_recent_order) {
            // Find if that order has the same product
            $product_found = $check_recent_order->Order_Item()
                ->where('product_id', $request->product_id)
                ->latest('created_at')
                ->first();

            if ($product_found) {
                $createdAt = Carbon::parse($product_found->created_at);
                $now = Carbon::now();

                $diffInMinutes = $createdAt->diffInMinutes($now);

                if ($diffInMinutes < 2) {
                    $response = [
                        'status' => 'error',
                        'is_show' => true,
                        'message' => 'আপনি এই প্রডাক্টি অলরেডি অর্ডার করেছেন। দয়া করে ২ মিনিট পর পুনরায় করুন। অথবা 01622351266 ওয়াটসঅ্যাপ এ যোগাযোগ করুন।',
                    ];
                    return response()->json($response);
                }
            }
        }
        $segment = null;
        try {
            //code...

            $product = products::find($request->product_id);
            $deliveryArea = delivery_areas::find($request->delivery_area);
            $deliveryCharge = $deliveryArea->charge;

            // Convert price and delivery charge to float for calculation
            $price = (float) ($product->discount_price ?? $product->price);
            $quantity = (float) $request->quantity;
            $delivery = (float) $deliveryCharge;

            // Calculate total
            $total = ($price * $quantity) + $delivery;
            $order = new Order();
            $order->name = $request->name;
            $order->phone = $phone ?? $request->phone;
            $order->address = $request->address;
            $order->delivery_area_id = $deliveryArea->id ?? null;
            $order->cod_percentage = '0';
            $order->cod_charge = '0';
            $order->subtotal = $total;
            $order->total = $total ?? '0';
            $order->discount = '0';
            $order->fee = $deliveryArea->charge ?? 0;

            $order->is_paid = false;
            $order->status = 'pending';
            $order->payment_status = 'unpaid';
            $order->payment_method = 'cash_on_delivery';
            if ($request->server('REMOTE_ADDR')) {
                $order->ip_address = $request->server('REMOTE_ADDR');
            }

            if ($request->server('HTTP_USER_AGENT')) {
                $order->user_agent = $request->server('HTTP_USER_AGENT');
            }

            if ($extra_data) {
                $order->json_data = $extra_data;
            }
            $order->save();

            $orderItem = new Order_Item();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $request->product_id;
            $orderItem->price = $product->discount_price ?? $product->price;
            $orderItem->quantity = $request->quantity;
            if ($request->has('size')) {

                $orderItem->options = ['size' => $request->size ?? ''];
            }

            $orderItem->save();

            $product->quantity = (float) $product->quantity - (float) $request->quantity;
            if ($product->quantity <= 0) {
                $product->stock_status = 'out_of_stock';
            }
            $product->save();

            $customer = Customer::where('phone', $request->phone)->first();
            if (!$customer) {
                $customer = new Customer();
                $customer->first_name = $request->name;
                $customer->phone = $request->phone;
                $customer->address = $request->address;
                $customer->save();
                HomeController::updateCustomerAddress($customer->id, $request->address);
            }
            $customer->orders()->attach($order->id);
            $order_received_url = route('order.received', ['order' => $order->id]);
            $segment_name = $product?->segments?->first()?->name ?? null;
            if (!$segment_name) {
                $segment_name = strtolower($segment_name);
            }
            $order->trackingEvent()->create([
                'is_fired' => false,
                'tud_id' => $_COOKIE['_fbp'] ?? null,
                'tracking_id' => $_COOKIE['custom_fbc'] ?? null,
                'event_name' => 'Purchase',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'referrer' => $request->header('Referer'),
                'url' => $order_received_url,
                'segment' => $segment_name,
            ]);
            $deviceId = $request->cookie('_sfdid');
            if ($deviceId) {
                OrderDraft::where('device_id', $deviceId)->delete();
            }
            $response = [
                'status' => 'success',
                'is_show' => true,
                'message' => 'অর্ডার সফলভাবে গ্রহন করা হয়েছে',
                'redirect_url' => route('order.received', ['order' => $order->id]),
                'order' => $order
            ];
        } catch (\Throwable $th) {
            //throw $th;


            $response = [
                'status' => 'error',
                'is_show' => true,
                'message' => response()->json($th->getMessage()),
            ];

        }
        return response()->json($response);
    }

    public function landingOrder(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'phone'             => 'required|string',
            'address'           => 'required|string',
            'items'             => 'required|array|min:1',
            'items.*.priceNum'  => 'required|numeric|min:0',
            'items.*.finalPrice'=> 'required|numeric|min:0',
            'items.*.qty'       => 'required|integer|min:1',
        ]);

        $phone = preg_replace('/\D/', '', $request->phone);
        if (str_starts_with($phone, '88') && strlen($phone) > 11) {
            $phone = substr($phone, 2);
        }
        if (strlen($phone) != 11) {
            return response()->json([
                'status'  => 'error',
                'message' => 'নাম্বার চেক করুন, আপনার নাম্বার ১১ ডিজিট হতে হবে',
            ], 422);
        }

        try {
            $items    = $request->items;
            $subtotal = collect($items)->sum(fn($i) => $i['priceNum'] * $i['qty']);
            $discount = collect($items)->sum(fn($i) => ($i['priceNum'] - $i['finalPrice']) * $i['qty']);
            $total    = $subtotal - $discount;

            $order = new Order();
            $order->name           = $request->name;
            $order->phone          = $phone;
            $order->address        = $request->address;
            $order->notes          = $request->note;
            $order->subtotal       = $subtotal;
            $order->discount       = $discount;
            $order->total          = $total;
            $order->fee            = 0;
            $order->cod_percentage = '0';
            $order->cod_charge     = '0';
            $order->is_paid        = ($request->payment_method === 'bkash');
            $order->status         = 'pending';
            $order->payment_status = ($request->payment_method === 'bkash') ? 'paid' : 'unpaid';
            $order->payment_method = $request->payment_method ?? 'cash_on_delivery';
            $order->transaction_id = $request->trx_id;
            $order->ip_address     = $request->server('REMOTE_ADDR');
            $order->user_agent     = $request->server('HTTP_USER_AGENT');
            $order->json_data      = [
                'source'          => 'landing_page',
                'landing_page'    => $request->landing_page ?? 'unknown',
                'delivery_method' => $request->delivery_method,
                'raw_items'       => $items,
            ];
            $order->save();

            foreach ($items as $item) {
                $productId = isset($item['product_id']) && $item['product_id'] ? (int) $item['product_id'] : null;
                $product   = $productId ? products::find($productId) : null;

                $orderItem             = new Order_Item();
                $orderItem->order_id   = $order->id;
                $orderItem->product_id = $productId;
                $orderItem->price      = $item['finalPrice'];
                $orderItem->quantity   = $item['qty'];
                $orderItem->options    = [
                    'package_label' => $item['label'] ?? null,
                    'kg'            => $item['kg'] ?? null,
                ];
                $orderItem->save();

                if ($product) {
                    $product->quantity = max(0, (float) $product->quantity - (float) $item['qty']);
                    if ($product->quantity <= 0) {
                        $product->stock_status = 'out_of_stock';
                    }
                    $product->save();
                }
            }

            $customer = Customer::where('phone', $phone)->first();
            if (!$customer) {
                $customer            = new Customer();
                $customer->first_name = $request->name;
                $customer->phone     = $phone;
                $customer->address   = $request->address;
                $customer->save();
                HomeController::updateCustomerAddress($customer->id, $request->address);
            }
            $customer->orders()->attach($order->id);

            return response()->json([
                'status'   => 'success',
                'message'  => 'অর্ডার সফলভাবে গ্রহন করা হয়েছে',
                'order_id' => $order->id,
            ]);
        } catch (\Throwable $th) {
            Log::error('landingOrder error: ' . $th->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'কিছু একটা ভুল হয়েছে। আবার চেষ্টা করুন।',
            ], 500);
        }
    }

    public function Purchase(Request $request)
    {
        $response = [];
        if (!$request->phone || $request->phone == '') {
            return response()->json([
                'status' => 'error',
                'is_show' => true,
                'message' => 'আপনার ফোন নাম্বারের ঘর খালি রয়েছে। আপনার ফোন নাম্বার দিয়ে অর্ডার করুন',
            ]);
        } elseif (!is_numeric($request->phone)) {

            return response()->json([
                'status' => 'error',
                'is_show' => true,
                'message' => 'আপনার ফোন নাম্বার একটি সংখ্যা হতে হবে',
            ]);
        } elseif (strlen($request->phone) != 11) {

            return response()->json([
                'status' => 'error',
                'is_show' => true,
                'message' => 'নাম্বার চেক করুন, আপনার নাম্বার ১১ ডিজিট হতে হবে',
            ]);
        } elseif ($request->address == '' || $request->address == null) {

            return response()->json([
                'status' => 'error',
                'is_show' => true,
                'message' => 'আপনার ঠিকানা দিয়ে অর্ডার করুন',
            ]);
        }

        $phone = preg_replace('/\D/', '', $request->phone);
        if (str_starts_with($phone, '88') && strlen($phone) > 11) {
            $phone = substr($phone, 2);
        }
        if (strlen($phone) != 11) {
            return response()->json([
                'status' => 'error',
                'is_show' => true,
                'message' => 'নাম্বার চেক করুন, আপনার নাম্বার ১১ ডিজিট হতে হবে',
            ]);
        }


        $extra_data = [];
        $extra_data['order_data'] = $request->all();
        // $check_recent_order = Order::where('phone', $phone)
        //     ->where('status', 'pending')
        //     ->latest('created_at')
        //     ->first();

        // $diffInMinutes = 0;

        // if ($check_recent_order) {
        //     // Find if that order has the same product
        //     $product_found = $check_recent_order->Order_Item()
        //         ->where('product_id', $request->product_id)
        //         ->latest('created_at')
        //         ->first();

        //     if ($product_found) {
        //         $createdAt = Carbon::parse($product_found->created_at);
        //         $now = Carbon::now();

        //         $diffInMinutes = $createdAt->diffInMinutes($now);

        //         if ($diffInMinutes < 2) {
        //             $response = [
        //                 'status' => 'error',
        //                 'is_show' => true,
        //                 'message' => 'আপনি এই প্রডাক্টি অলরেডি অর্ডার করেছেন। দয়া করে ২ মিনিট পর পুনরায় করুন। অথবা 01622351266 ওয়াটসঅ্যাপ এ যোগাযোগ করুন।',
        //             ];
        //             return response()->json($response);
        //         }
        //     }
        // }
        $segment = null;
        try {
            //code...

            // $product = products::find($request->product_id);
            $deliveryArea = delivery_areas::find($request->delivery_area);
            $deliveryCharge = $deliveryArea->charge;

            // Convert price and delivery charge to float for calculation
            $delivery = (float) $deliveryCharge;
            $subTotal = 0;
            $cartItems = collect();
            if (!$request->orderCart) {
                return response()->json([
                    'status' => 'error',
                    'is_show' => true,
                    'message' => 'Select Product First',
                ]);
            }
            $items = $request->orderCart['cartItems'] ?? [];
            if (empty($items) || count($items) == 0) {
                return response()->json([
                    'status' => 'error',
                    'is_show' => true,
                    'message' => 'অনুগ্রহ করে প্রডাক্ট সেলেক্ট করে তার পর অর্ডার করুন',
                ]);
            }
            foreach ($items as $cartItem) {
                $product = products::find($cartItem['id']);
                $price = (float) ($product->discount_price ?? $product->price);
                $quantity = (int) $cartItem['quantity'];

                $subTotal += ($price * $quantity);

                $cartItems->push([
                    'product_id' => $product->id,
                    'price' => $price,
                    'quantity' => $quantity
                ]);
            }
            //  return response()->json([
            //     'status' => 'error',
            //     'is_show' => false,
            //     'message' => 'debug',
            //     'cartItems' => $cartItems

            // ]);


            // Calculate total
            $total =$subTotal + $delivery;
            $order = new Order();
            $order->name = $request->name;
            $order->phone = $phone ?? $request->phone;
            $order->address = $request->address;
            $order->delivery_area_id = $deliveryArea->id ?? null;
            $order->cod_percentage = '0';
            $order->cod_charge = '0';
            $order->subtotal = $subTotal;
            $order->total = $total ?? '0';
            $order->discount = '0';
            $order->fee = $deliveryArea->charge ?? 0;

            $order->is_paid = false;
            $order->status = 'pending';
            $order->payment_status = 'unpaid';
            $order->payment_method = 'cash_on_delivery';
            if ($request->server('REMOTE_ADDR')) {
                $order->ip_address = $request->server('REMOTE_ADDR');
            }

            if ($request->server('HTTP_USER_AGENT')) {
                $order->user_agent = $request->server('HTTP_USER_AGENT');
            }

            if ($extra_data) {
                $order->json_data = $extra_data;
            }
            $order->save();

            foreach ($cartItems as $cartItem) {


                $orderItem = new Order_Item();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $cartItem['product_id'];
                $orderItem->price = $cartItem['price'];
                $orderItem->quantity = $cartItem['quantity'];
                // if ($request->has('size')) {

                //     $orderItem->options = json_encode(['size' => $request->size ?? '']);
                // }

                $orderItem->save();
            }
            $product->quantity = (float) $product->quantity - (float) $request->quantity;
            if ($product->quantity <= 0) {
                $product->stock_status = 'out_of_stock';
            }
            $product->save();

            $customer = Customer::where('phone', $request->phone)->first();
            if (!$customer) {
                $customer = new Customer();
                $customer->first_name = $request->name;
                $customer->phone = $request->phone;
                $customer->address = $request->address;
                $customer->save();
                HomeController::updateCustomerAddress($customer->id, $request->address);
            }
            $customer->orders()->attach($order->id);
            $order_received_url = route('order.received', ['order' => $order->id]);
            $segment_name = $product?->segments?->first()?->name ?? null;
            if (!$segment_name) {
                $segment_name = strtolower($segment_name);
            }
            $order->trackingEvent()->create([
                'is_fired' => false,
                'tud_id' => $_COOKIE['_fbp'] ?? null,
                'tracking_id' => $_COOKIE['custom_fbc'] ?? null,
                'event_name' => 'Purchase',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'referrer' => $request->header('Referer'),
                'url' => $order_received_url,
                'segment' => $segment_name,
            ]);
            $deviceId = $request->cookie('_sfdid');
            if ($deviceId) {
                OrderDraft::where('device_id', $deviceId)->delete();
            }
            $response = [
                'status' => 'success',
                'is_show' => true,
                'message' => 'অর্ডার সফলভাবে গ্রহন করা হয়েছে',
                'redirect_url' => route('order.received', ['order' => $order->id]),
                'order' => $order
            ];
        } catch (\Throwable $th) {
            //throw $th;


            $response = [
                'status' => 'error',
                'is_show' => true,
                'message' => response()->json($th->getMessage()),
            ];

        }
        return response()->json($response);
    }

    //cart
    public function view(Request $request){

    return view('cart');
    }
     public function checkout()
    {
            //  $cart = ModelsCart::where('device_id', 1)->first();

        // $cartManager = new CartManager();
        // $cartData = $cartManager->getCart();

        // if (empty($cartData->items)) {
        //     return redirect()
        //         ->route('cart.view')
        //         ->with('cart_error', 'Your cart is empty. Add some products before checkout.');
        // }

        // $bkashNumber = 01166555555555;

        // $deliveryAreas = delivery_areas::orderBy('id', 'asc')->get();
        return view('checkout');
    }

}
