<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use Illuminate\Support\Facades\DB;
use App\Models\Analytic;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\PoliceStation;
use App\Models\Segment;
use App\Models\Size;
use App\Models\Visit;
use App\Models\Zipcode;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\products;
use App\Models\delivery_areas;
use App\Models\Order;
use App\Models\Order_Item;
use App\Models\User;
use App\Models\Slide;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Device;
use App\Models\LandingPage;
use App\Models\Media;
use App\Models\OrderDraft;
use App\Models\State;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use ShahariarAhmad\CourierFraudCheckerBd\Services\PathaoService;
use ShahariarAhmad\CourierFraudCheckerBd\Services\SteadfastService;

class AdminController extends Controller
{
    public function terminal()
    {
        return view('admin.terminal.index');
    }

    public function users()
    {
        return view('admin.users.index');
    }

    public function account()
    {
        return view('admin.account.index');
    }

    public function deleteProductMedia(int $id)
    {
        $media = Media::findOrFail($id);

        // Old-style product gallery images (path includes 'storage/' prefix) — safe to delete.
        // Media-library items (path like 'media/...') are shared: only unlink, don't delete the file.
        if (str_starts_with($media->path, 'storage/')) {
            $filePath = public_path($media->path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $media->delete();
        } else {
            $media->mediable_id   = null;
            $media->mediable_type = null;
            $media->category      = null;
            $media->save();
        }

        return response()->json(['success' => true]);
    }
    public function index()
    {
        $pending_orders      = Order::where('status', 'pending')->count();
        $pending_orders_sum  = Order::where('status', 'pending')->sum('total');
        $delivered_orders    = Order::where('status', 'delivered')->count();
        $delivered_orders_sum = Order::where('status', 'delivered')->sum('total');
        $cancelled_orders    = Order::where('status', 'cancelled')->count();
        $cancelled_orders_sum = Order::where('status', 'cancelled')->sum('total');
        $total_orders        = Order::count();
        $total_orders_sum    = Order::sum('total');
        $orders              = Order::orderBy('created_at', 'desc')->limit(8)->get();
        $active_users        = Device::whereBetween('last_activity', [Carbon::now()->subMinutes(5), Carbon::now()])->count();

        $ordersThisMonth  = Order::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $revenueThisMonth = Order::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)
            ->where('status', '!=', 'cancelled')->sum('total');

        $monthlyStats = Order::selectRaw('DATE_FORMAT(created_at, "%b %Y") as label, SUM(total) as revenue, COUNT(*) as orders_count')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->where('status', '!=', 'cancelled')
            ->groupByRaw('DATE_FORMAT(created_at, "%Y-%m"), DATE_FORMAT(created_at, "%b %Y")')
            ->orderByRaw('MIN(created_at)')
            ->get();

        $topCustomers = Customer::withCount('orders')
            ->withSum('orders', 'total')
            ->having('orders_count', '>', 0)
            ->orderByDesc('orders_sum_total')
            ->limit(5)
            ->get();

        $topProducts = DB::table('order__items')
            ->join('products', 'products.id', '=', 'order__items.product_id')
            ->select('products.id', 'products.name', 'products.image')
            ->selectRaw('SUM(order__items.total) as revenue, SUM(order__items.quantity) as units')
            ->groupBy('products.id', 'products.name', 'products.image')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        $recentDrafts = OrderDraft::withCount('items')->latest()->limit(5)->get();

        $recentCarts = \App\Models\Cart::with(['items.product', 'device'])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.index', compact(
            'pending_orders', 'delivered_orders', 'cancelled_orders',
            'total_orders', 'pending_orders_sum', 'delivered_orders_sum',
            'cancelled_orders_sum', 'total_orders_sum', 'orders', 'active_users',
            'ordersThisMonth', 'revenueThisMonth', 'monthlyStats',
            'topCustomers', 'topProducts', 'recentDrafts', 'recentCarts'
        ));
    }
    public function login()
    {
        return view('admin.login');
    }


    //Products
    public function products(Request $request)
    {
        $search = $request->search;
        if ($search) {
            $products = products::where('name', 'like', '%' . $search . '%')
                ->orWhere('id', 'like', '%' . $search . '%')
                ->orWhere('price', 'like', '%' . $search . '%')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

        } else {

            $products = products::orderBy('created_at', 'desc')->paginate(20);
        }

        return view('admin.products', compact('products'));
    }

    public function productsAdd($id = null)
    {
        $product = null;
        $segment_id = null;

        if ($id) {
            $product = products::find($id);
            $segment_id = $product?->segments?->first()?->id;
        }
        $categories = Category::all();
        $segments = Segment::all();

        return view('admin.products-add', compact('categories', 'segments', 'product', 'segment_id'));
    }
    public function productStore(Request $request)
    {
        // return $request->all();
        $request->validate([
            'name'         => 'required',
            'price'        => 'required|numeric',
            'stock_status' => 'required|in:in_stock,out_of_stock',
            'quantity'     => 'required|integer',
            'image'        => 'nullable|string',
            'segment'      => 'required',
        ]);
        $product = new products();

        $product->name           = $request->name;
        $product->price          = $request->price;
        $product->purchase_price = $request->purchase_price ?: null;
        if ($request->discount_price) {
            $product->discount_price = $request->discount_price;
        }
        $slug = Str::slug($request->name);
        if (products::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . Carbon::now()->timestamp;
        }
        $product->slug        = $slug;
        $product->featured    = $request->featured ? true : false;
        $product->sku         = $request->sku ?: null;
        $product->is_redirected = $request->is_redirected ? true : false;
        $product->redirect_url  = $request->redirect_url ?: null;
        $product->stock_status  = $request->stock_status;
        $product->quantity      = $request->quantity;

        if ($request->filled('image')) {
            $product->image = $request->image;
        }
        if ($request->description) {
            $product->description = $request->description;
        }
        if ($request->short_description) {
            $product->short_description = $request->short_description;
        }
        if ($request->yt_video_url) {
            $product->yt_video_url = $request->yt_video_url;
        }

        $product->save();

        if ($request->has('sizes')) {
            foreach ($request->sizes as $size) {
                Size::create([
                    'products_id' => $product->id,
                    'name'        => $size['size'],
                    'quantity'    => $size['qty'] ?? 0,
                ]);
            }
        }

        // Link gallery images picked from the media library
        if ($request->filled('gallery_media_ids')) {
            Media::whereIn('id', $request->gallery_media_ids)
                ->update([
                    'mediable_id'   => $product->id,
                    'mediable_type' => products::class,
                    'category'      => 'product_images',
                ]);
        }

        if ($request->has('categories')) {
            $product->categories()->attach($request->categories);
        }
        if ($request->has('segment')) {
            $product->segments()->attach($request->segment);
            $product->save();
        }
        return redirect()->route('admin.products')->with('status', 'Product Added Successfully');
    }

    public function generateProductThumbnailImage($image, $imageName)
    {
        $thumbnail_path = public_path('storage/images/products/thumbnails/');
        $image_path = public_path('storage/images/products/');
        if(!file_exists($thumbnail_path)) {
            mkdir($thumbnail_path, 0777, true);
        }
        if(!file_exists($image_path)) {
            mkdir($image_path, 0777, true);
        }

        $image = Image::read($image->path());
        $image->save($image_path . $imageName, 70);
        $image->save($thumbnail_path . $imageName, 70);

    }

    public function productEdit($id)
    {
        $product = products::find($id);
        $categories = Category::all();
        $segments = Segment::all();
        return view('admin.products-edit', compact('product', 'categories', 'segments'));
    }
    public function productUpdate(Request $request)
    {

        $request->validate([
            'name'         => 'required',
            'price'        => 'required|numeric',
            'stock_status' => 'required|in:in_stock,out_of_stock',
            'featured'     => 'boolean',
            'quantity'     => 'required|integer',
            'image'        => 'nullable|string',
        ]);

        $product = products::find($request->id);
        if (!$product) { abort(404); }

        $product->name           = $request->name;
        $product->price          = $request->price;
        $product->purchase_price = $request->purchase_price ?: null;

        if ($request->slug) {
            $slug = $request->slug;
            if (products::where('slug', $slug)->whereNotIn('id', [$product->id])->exists()) {
                $slug = $slug . '-' . Carbon::now()->timestamp;
            }
            $product->slug = $slug;
        }

        $product->discount_price  = $request->discount_price ?: null;
        $product->sku             = $request->sku ?: null;
        $product->is_redirected   = $request->is_redirected ? true : false;
        $product->redirect_url    = $request->redirect_url ?: null;
        $product->stock_status    = $request->stock_status;
        $product->featured        = $request->featured ? true : false;
        $product->quantity        = $request->quantity;
        $product->status          = $request->has('status');

        if ($request->filled('image')) {
            $product->image = $request->image;
        }
        if ($request->description) {
            $product->description = $request->description;
        }
        if ($request->short_description) {
            $product->short_description = $request->short_description;
        }
        if ($request->yt_video_url) {
            $product->yt_video_url = $request->yt_video_url;
        }

        $product->save();

        $product->sizes()->delete();
        if ($request->has('sizes')) {
            foreach ($request->sizes as $size) {
                Size::create([
                    'products_id' => $product->id,
                    'name'        => $size['size'],
                    'quantity'    => $size['qty'] ?? 0,
                ]);
            }
        }

        // Link newly picked gallery images — existing gallery is untouched
        if ($request->filled('gallery_media_ids')) {
            Media::whereIn('id', $request->gallery_media_ids)
                ->update([
                    'mediable_id'   => $product->id,
                    'mediable_type' => products::class,
                    'category'      => 'product_images',
                ]);
        }

        if ($request->has('categories') && !empty($request->categories)) {
            $product->categories()->sync($request->categories);
        }
        if ($request->has('segment')) {
            $product->segments()->sync($request->segment);
            $product->save();
        }
        return redirect()->route('admin.products')->with('status', 'Product Updated Successfully');
    }

    public function productDelete($id)
    {
        $product = products::find($id);
        if (File::exists(public_path('storage/images/products/thumbnails/' . $product->image))) {
            File::delete(public_path('storage/images/products/thumbnails/' . $product->image));
            File::delete(public_path('storage/images/products/' . $product->image));

        }
        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product Deleted Successfully');
    }
    public function coupons()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.coupons', compact('coupons'));
    }
    public function couponAdd()
    {
        return view('admin.coupons-add');
    }
    public function couponStore(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date_format:Y-m-d',
        ]);
        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status', 'Coupon Added Successfully');
    }
    public function couponEdit($id)
    {
        $coupon = Coupon::find($id);
        return view('admin.coupons-edit', compact('coupon'));
    }
    public function couponUpdate(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date_format:Y-m-d',
        ]);
        $coupon = Coupon::find($request->id);
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status', 'Coupon Updated Successfully');
    }
    public function couponDelete($id)
    {
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('status', 'Coupon Deleted Successfully');
    }
    public function orders(Request $request)
    {
        return view('admin.orders');
    }

    public function orderDrafts()
    {
        return view('admin.order-drafts');
    }

    public function draftCleanup()
    {
        $deleted = OrderDraft::cleanCompletedDrafts();
        return response()->json(['deleted' => $deleted]);
    }

    public function orderDetails($id)
    {
        $order = Order::find($id);

        if (!$order) {
            abort(404);
        }

        $phone = $order->phone ?? '';
        if (strlen($phone) == 11) {
            try {
                $order->fraud_check_steadfast = collect((new SteadfastService())->steadfast($phone));
            } catch (\Throwable $e) {
                $order->fraud_check_steadfast = collect(['error' => $e->getMessage()]);
            }

            try {
                $order->fraud_check_pathao = collect((new PathaoService())->pathao($phone));
            } catch (\Throwable $e) {
                $order->fraud_check_pathao = collect(['error' => $e->getMessage()]);
            }
        }

        $customer = $order->customer->first();
        $orderItems = Order_Item::where('order_id', $id)->paginate(10);
        $products = products::all();
        return view('admin.order-details', compact('order', 'orderItems', 'products', 'customer'));
    }

    public function orderStatusUpdate(Request $request)
    {



        $order = Order::find($request->order_id);
        if ($order->status == $request->status) {

            return redirect()->route('admin.orders.details', $order->id)->with('status', 'Order Status Already Updated');

        } else {

            if ($request->status == 'pending') {
                $order->status = $request->status;
                $order->save();
            }
            if ($request->status == 'on_hold') {
                $order->status = $request->status;
                $order->save();
            }
            if ($request->status == 'confirmed') {
                $order->status = $request->status;
                $order->save();
            }
            if ($request->status == 'processing') {
                $order->status = $request->status;
                $order->save();
            }
            if ($request->status == 'in_transit') {
                $order->status = $request->status;
                $order->save();
            }
            if ($request->status == 'delivered') {
                $order->status = $request->status;
                $order->delivery_date = Carbon::now();
                $order->save();
            }

            if ($request->status == 'cancelled') {
                $order->status = $request->status;
                $order->cancelled_date = Carbon::now();
                $order->save();
            }

            if ($request->status == 'returned') {
                $order->status = $request->status;
                $order->save();
            }



        }
        return redirect()->route('admin.orders.details', $order->id)->with('status', 'Order Status Updated Successfully');
    }
    public function bulkOrderStatusUpdate(Request $request)
    {




        foreach ($request->ids as $id) {
            $order = Order::find($id);

            if ($request->status == 'pending') {
                $order->status = $request->status;
                $order->save();
            }
            if ($request->status == 'on_hold') {
                $order->status = $request->status;
                $order->save();
            }
            if ($request->status == 'confirmed') {
                $order->status = $request->status;
                $order->save();
            }
            if ($request->status == 'processing') {
                $order->status = $request->status;
                $order->save();
            }
            if ($request->status == 'in_transit') {
                $order->status = $request->status;
                $order->save();
            }
            if ($request->status == 'delivered') {
                $order->status = $request->status;
                $order->delivery_date = Carbon::now();
                $order->save();
            }

            if ($request->status == 'cancelled') {
                $order->status = $request->status;
                $order->cancelled_date = Carbon::now();
                $order->save();
            }

            if ($request->status == 'returned') {
                $order->status = $request->status;
                $order->save();
            }
            if ($request->status == 'deleted') {
                $order->status = $request->status;
                $order->save();
            }



        }
        return response()->json(['success' => 'Order Status Updated Successfully']);
    }

    public function deleteOrder($id)
    {
        $order = Order::find($id);
        $orderItems = Order_Item::where('order_id', $id)->get();
        foreach ($orderItems as $orderItem) {
            $orderItem->delete();
        }
        $order->delete();
        return redirect()->route('admin.orders')->with('status', 'Order Deleted Successfully');
    }
    public function ordersoftdelete($id)
    {
        $order = Order::find($id);
        if ($order->status == 'deleted') {
            return redirect()->back()->with('status', 'Order Already Deleted');
        }
        $order->status = 'deleted';
        $order->save();
        return redirect()->back()->with('status', 'Order Deleted Successfully');
    }

    public function exportOrders(Request $request)
    {
        $order_status = $request->order_status ?? null;
        if ($request->has('order_status')) {

            $fileName = $order_status . '_orders_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        } else {
            $fileName = 'orders_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        }

        return Excel::download(new OrderExport($order_status), $fileName);
    }
    public function updateOrder(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found');
        }
        $order->Order_Item()->delete();
        // $order->Order_Item()
        $orderedProducts = products::whereIn('id', $request->products)->get();
        foreach ($orderedProducts as $orderedProduct) {
            $orderItem = new Order_Item();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $orderedProduct->id;
            $orderItem->quantity = $request->order_items[$orderedProduct->id]['quantity'];
            $orderItem->price = $orderedProduct->price;
            $orderItem->options = json_encode(['size' => $request->order_items[$orderedProduct->id]['size']]);
            $orderItem->save();
        }
        $subtotal = $orderedProducts->sum(function ($product) use ($request) {
            return (float) ($product->discount_price ?? $product->price) * (float) $request->order_items[$product->id]['quantity'];
        });
        $order->subtotal = $subtotal;
        $order->discount = $request->discount;
        $order->fee = $request->delivery_charge;
        $order->total = ($subtotal + (float) $request->delivery_charge) - (float) $request->discount;

        $order->save();



        $order->save();

        // return $request->all();
        return redirect()->back()->with('status', 'Order Updated Successfully');
    }
    public function updateOrderDetails(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found');
        }
        $order->name = $request->name;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->notes = $request->note;
        $order->save();
        return redirect()->back()->with('status', 'Order Details Updated Successfully');
    }
    public function orderAdd()
    {
        $products = products::all();
        $delivery_areas = delivery_areas::all();

        return view('admin.order-add', compact('products', 'delivery_areas'));
    }
    public function orderStore(Request $request)
    {
        $order = new Order();
        $order->name = $request->name;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->notes = $request->note;
        $order->save();
        return redirect()->route('admin.orders')->with('status', 'Order Created Successfully');
    }
    public function deliveryAreas()
    {
        $deliveryAreas = delivery_areas::orderBy('id', 'desc')->paginate(10);
        return view('admin.delivery-areas', compact('deliveryAreas'));
    }
    public function deliveryAreaAdd()
    {
        return view('admin.delivery-areas-add');
    }
    public function deliveryAreaStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'charge' => 'required|numeric',
        ]);
        $deliveryArea = new delivery_areas();
        $deliveryArea->name = $request->name;
        $deliveryArea->charge = $request->charge;
        $deliveryArea->save();
        return redirect()->route('admin.deliveryareas')->with('status', 'Delivery Area Added Successfully');
    }
    public function deliveryAreaEdit($id)
    {
        $deliveryArea = delivery_areas::find($id);
        return view('admin.delivery-areas-edit', compact('deliveryArea'));
    }
    public function deliveryAreaUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'charge' => 'required|numeric',
        ]);
        $deliveryArea = delivery_areas::find($request->id);
        $deliveryArea->name = $request->name;
        $deliveryArea->charge = $request->charge;
        $deliveryArea->save();
        return redirect()->route('admin.deliveryareas')->with('status', 'Delivery Area Updated Successfully');
    }

    public function deliveryAreaDelete($id)
    {
        $deliveryArea = delivery_areas::find($id);
        $deliveryArea->delete();
        return redirect()->route('admin.deliveryareas')->with('status', 'Delivery Area Deleted Successfully');
    }

    //Slides
    public function slides()
    {
        $slides = Slide::orderBy('id', 'desc')->paginate(10);
        return view('admin.slides', compact('slides'));
    }
    public function slideAdd()
    {
        return view('admin.slides-add');
    }
    public function slideStore(Request $request)
    {
        $this->validate($request, [
            'title'    => 'required',
            'subtitle' => 'required',
            'tagline'  => 'required',
            'image'    => 'required|string',
        ]);

        $slide = new Slide();
        $slide->title    = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->tagline  = $request->tagline;
        $slide->image    = $request->image;
        $slide->save();

        return redirect()->route('admin.slides')->with('status', 'Slide Added Successfully');
    }

    public function GenerateSlideThumbnailImage($image, $imageName)
    {
        $thumbnail_path = public_path('storage/images/slides/thumbnails/');
        $image_path = public_path('storage/images/slides/');
        $image = Image::read($image->path());
        $image->cover(602, 602, 'top');
        $image->resize(602, 602, function ($constraint) {
            $constraint->aspectRatio();
        });
        $image->save($image_path . $imageName);
        $image->cover(202, 202, 'top');
        $image->resize(202, 202, function ($constraint) {
            $constraint->aspectRatio();
        });
        $image->save($thumbnail_path . $imageName);
    }
    public function slideEdit($id)
    {
        $slide = Slide::find($id);
        return view('admin.slides-edit', compact('slide'));
    }
    public function slideUpdate(Request $request, $id)
    {
        $this->validate($request, [
            'title'    => 'required',
            'subtitle' => 'required',
            'tagline'  => 'required',
            'image'    => 'required|string',
        ]);

        $slide = Slide::find($id);
        if (!$slide) { abort(404); }

        $slide->title    = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->tagline  = $request->tagline;
        if ($request->filled('image')) {
            $slide->image = $request->image;
        }
        $slide->save();

        return redirect()->route('admin.slides')->with('status', 'Slide Updated Successfully');
    }
    public function slideDelete($id)
    {
        $slide = Slide::find($id);
        if (File::exists(public_path('storage/images/slides/thumbnails/' . $slide->image))) {
            File::delete(public_path('storage/images/slides/thumbnails/' . $slide->image));
            File::delete(public_path('storage/images/slides/' . $slide->image));
        }
        $slide->delete();
        return redirect()->route('admin.slides')->with('status', 'Slide Deleted Successfully');
    }
    //Analytics
    public function gAnalaytics()
    {
        $google_analytics = Analytic::where('slug', 'google-analytics')->first();
        return view('admin.google-analytics', compact('google_analytics'));
    }
    public function gAnalyticsUpdate(Request $request)
    {

        $google_analytics = Analytic::find($request->id);
        $google_analytics->code = $request->code ?? '';
        $google_analytics->save();
        return redirect()->route('admin.google.analytics')->with('status', 'Google Analytics Code Updated Successfully');
    }
    public function fbPixels()
    {
        $facebook_pixels = Analytic::where('slug', 'facebook-pixels')->first();
        return view('admin.facebook-pixels', compact('facebook_pixels'));
    }
    public function fbPixelsUpdate(Request $request)
    {
        $facebook_pixels = Analytic::find($request->id);
        $facebook_pixels->code = $request->code ?? '';
        $facebook_pixels->save();
        return redirect()->route('admin.facebook.pixels')->with('status', 'Facebook Pixels Code Updated Successfully');
    }

    public function analytics()
    {
        // Total stats
        $totalVisitors = Visit::distinct('ip_address')->count('ip_address');
        $totalPageViews = Visit::count();

        $todayVisitors = Visit::whereDate('created_at', today())
            ->distinct('ip_address')->count('ip_address');
        $todayPageViews = Visit::whereDate('created_at', today())->count();

        // Device stats
        $devices = Visit::select('device')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('device')
            ->get();

        // Referrers stats
        $referrers = Visit::select('referrer')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('referrer')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Pages stats
        $pages = Visit::select('page_url')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('page_url')
            ->orderByDesc('total')
            ->take(10)
            ->get();
        // Pages stats
        $todaypages = Visit::whereDate('created_at', today())
            ->select('page_url')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('page_url')
            ->orderByDesc('total')
            ->get();

        // Locations stats
        $locations = Visit::select('country')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('country')
            ->orderByDesc('total')
            ->get();
        // cities
        $cities = Visit::select('city')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('city')
            ->orderByDesc('total')
            ->get();

        return view('admin.analytics', compact(
            'totalVisitors',
            'totalPageViews',
            'todayVisitors',
            'todayPageViews',
            'devices',
            'referrers',
            'pages',
            'locations',
            'cities',
            'todaypages',
        ));
    }

    //customers
    public function customers(Request $request)
    {
        $totalCustomers  = Customer::count();
        $newThisMonth    = Customer::whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)->count();
        $activeCustomers = Customer::has('orders')->count();
        $newLastMonth    = Customer::whereMonth('created_at', now()->subMonth()->month)
                                   ->whereYear('created_at', now()->subMonth()->year)->count();

        // Top 10 customers by total spend
        $topCustomers = Customer::withCount('orders')
            ->withSum('orders', 'total')
            ->having('orders_count', '>', 0)
            ->orderByDesc('orders_sum_total')
            ->limit(10)
            ->get();

        // Monthly new customers — last 7 months
        $monthlyGrowth = Customer::selectRaw('DATE_FORMAT(created_at, "%b %Y") as label, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupByRaw('DATE_FORMAT(created_at, "%Y-%m"), DATE_FORMAT(created_at, "%b %Y")')
            ->orderByRaw('DATE_FORMAT(MIN(created_at), "%Y-%m")')
            ->get();

        return view('admin.customers', compact(
            'totalCustomers', 'newThisMonth', 'activeCustomers',
            'newLastMonth', 'topCustomers', 'monthlyGrowth'
        ));
    }
    public function customerDetails($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            abort(404);
        }
        $orders = $customer->orders()->orderBy('created_at', 'desc')->paginate(10);
        // $order_count = $customerOrders->count();
        // $order_sum = $customerOrders->sum('total');
        $devices = $customer->devices()->orderBy('last_activity', 'desc')->paginate(10);
        $countries = Country::all();
        $states = State::all();
        $cities = City::all();
        return view('admin.customer-details', compact('customer', 'orders', 'countries', 'states', 'cities'));
    }
    public function updateCustomer(Request $request)
    {
        // return $request->all();
        $customer = Customer::find($request->customer_id);
        if (!$customer) {
            return redirect()->back()->with('error', 'Customer not found');
        }
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->city = $request->city;
        $customer->state = $request->state;
        $customer->country = $request->country;
        $customer->zip_code = $request->zip_code;
        $customer->street = $request->street;
        $customer->gender = $request->gender;
        $customer->save();
        return redirect()->back()->with('status', 'Customer Updated Successfully');
    }
    public function customerDelete($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return redirect()->back()->with('error', 'Customer not found');
        }
        $customer->delete();
        return redirect()->route('admin.customers')->with('status', 'Customer Deleted Successfully');
    }
    public function customerCreateFromOrder($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found');
        }
        $customer = Customer::where('phone', $order->phone)->first();
        if ($customer) {
            $customer->orders()->attach($order->id);

            return redirect()->route('admin.customers.details', $customer->id)->with('status', 'Customer Already Exists');
        } else {


            $customer = new Customer();
            $customer->first_name = $order->name;
            $customer->phone = $order->phone;
            $customer->email = $order->email;
            $customer->address = $order->address;
            $customer->save();
            $customer->orders()->attach($order->id);
        }
        return redirect()->route('admin.customers.details', $customer->id)->with('status', 'Customer Created Successfully');

    }
    public function locations(Request $request)
    {
        $states = collect();
        $cities = collect();
        $police_stations = collect();
        $zipcodes = collect();
        $area_keywords = collect();
        $countries = Country::orderBy('name', 'asc')->get();
        if ($request->country_id) {
            $states = Country::find($request->country_id)->states;
        }
        if ($request->state_id) {
            $cities = State::find($request->state_id)->cities;
        }
        if ($request->city_id) {
            $police_stations = City::find($request->city_id)->ps;
        }
        if ($request->ps_id) {
            $zipcodes = PoliceStation::find($request->ps_id)->zipcodes;
        }
        if ($request->zipcode_id) {
            $area_keywords = Zipcode::find($request->zipcode_id)->area_keywords;
        }
        // dd($area_keywords);

        return view('admin.location', compact('countries', 'states', 'cities', 'police_stations', 'zipcodes', 'area_keywords'));
    }
    /**
     * Devices
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     *
     * @return \Illuminate\Contracts\View\Factory
     */
    public function devices(Request $request)
    {
        if ($request->has('search')) {
            $search = $request->search;
            $devices = Device::where('device_id', 'LIKE', '%' . $search . '%')
                ->orWhere('platform', 'LIKE', '%' . $search . '%')
                ->orWhere('browser', 'LIKE', '%' . $search . '%')
                ->orWhereHas('customer', function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('phone', 'LIKE', '%' . $search . '%');
                })
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
        } else {
            $devices = Device::orderBy('last_activity', 'desc')->paginate(10);
        }
        $devices_count = Device::count();
        return view('admin.devices', compact('devices', 'devices_count'));
    }
    public function campaigns()
    {
        $campaigns = Campaign::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.campaigns', compact('campaigns'));
    }
    public function campaignLandingPageEdit($id)
    {
        $campaign = Campaign::find($id);
        if (!$campaign) {
            abort(404);
        }

        $json = json_decode($campaign->json_data);

        // $json = json_decode(file_get_contents(base_path('resources/views/templates/landingpages/seldom_zaynah_eid.json'))); // object
        $page = $json ?? [];
        // dd($page->sections->hero->features[0]);
        return view('admin.campaign-landing-page-edit', compact('campaign', 'page'));
    }
    public function campaignLandingPageUpdate(Request $request, $id)
    {
        $campaign = Campaign::find($id);
        if (!$campaign) {
            abort(404);
        }
        $fields = request('fields', []);
        $json = json_decode($campaign->json_data); // object
        $sections = $json->sections ?? [];
        // dd($fields);

        foreach ($fields as $key => $value) {
            $cleanKey = trim($key);   // 🔥 THIS LINE FIXES EVERYTHING
            data_set($sections, $cleanKey, $value);
        }

        $json->sections = $sections;
        $campaign->json_data = json_encode($json);
        $campaign->save();

        // $campaign->save();
        return redirect()->route('admin.campaigns')->with('status', 'Landing Page Updated Successfully');
    }
    public function campaignAdd()
    {
        $pages = LandingPage::all();
        return view('admin.campaigns-add', compact('pages'));
    }

    public function campaignStore(Request $request)
    {
        $data = $request->all();
        // return $data;
        $this->validate($request, [
            'name' => 'required',
            'landing_page_id' => 'required',
            'slug' => 'required',
        ]);
        $page = LandingPage::find($request->landing_page_id);

        Campaign::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'json_data' => $page->json_data,
            'status' => $request->status,
            'view_file' => $page->view_file,
            'landing_page_id' => $request->landing_page_id
        ]);
        return redirect()->route('admin.campaigns')->with('status', 'Campaign Created Successfully');
    }
    public function campaignEdit($id)
    {
        $campaign = Campaign::find($id);
        if (!$campaign) {
            abort(404);
        }

        $pages = LandingPage::all();
        return view('admin.campaigns-edit', compact('campaign', 'pages'));
    }
    public function campaignUpdate(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required',

        ]);
        $campaign = Campaign::find($id);
        if (!$campaign) {
            abort(404);
        }
        $campaign->name = $request->name;
        $campaign->slug = $request->slug;
        $campaign->status = $request->status;
        $campaign->save();
        return redirect()->route('admin.campaigns')->with('status', 'Campaign Updated Successfully');

    }
    public function campaignDelete($id)
    {
        $campaign = Campaign::find($id);
        if (!$campaign) {
            abort(404);
        }
        $campaign->delete();
        return redirect()->route('admin.campaigns')->with('status', 'Campaign Deleted Successfully');
    }

    public function campaignToggleStatus($id)
    {
        $campaign = Campaign::find($id);
        if (!$campaign) { abort(404); }
        $campaign->status = $campaign->status == 1 ? 0 : 1;
        $campaign->save();
        return response()->json(['status' => $campaign->status]);
    }

    public function campaignCopy($id)
    {
        $campaign = Campaign::find($id);
        if (!$campaign) {
            abort(404);
        }
        $copy = $campaign->replicate();
        $copy->name   = $campaign->name . ' (Copy)';
        $copy->slug   = $campaign->slug . '-copy-' . time();
        $copy->status = 0;
        $copy->save();
        return redirect()->route('admin.campaigns')->with('status', 'Campaign duplicated successfully.');
    }

    /**
     * Recursively merge $template into $campaign.
     * Campaign values always win for existing keys; new keys/indices from template are added.
     */
    private function deepMergeTemplate($template, $campaign)
    {
        if (is_object($template) && is_object($campaign)) {
            $result = clone $campaign;
            foreach (get_object_vars($template) as $key => $tVal) {
                if (property_exists($campaign, $key)) {
                    $result->$key = $this->deepMergeTemplate($tVal, $campaign->$key);
                } else {
                    $result->$key = $tVal;
                }
            }
            return $result;
        }
        if (is_array($template) && is_array($campaign)) {
            $result = $campaign;
            foreach ($template as $i => $tVal) {
                if (!array_key_exists($i, $campaign)) {
                    $result[$i] = $tVal;
                } else {
                    $result[$i] = $this->deepMergeTemplate($tVal, $campaign[$i]);
                }
            }
            return $result;
        }
        return $campaign; // scalar: campaign value wins
    }

    public function campaignSyncTemplate($id)
    {
        $campaign = Campaign::find($id);
        if (!$campaign) { abort(404); }

        $page = LandingPage::find($campaign->landing_page_id);
        if (!$page) {
            return redirect()->back()->with('sync_error', 'Source landing page not found for this campaign.');
        }

        $templatePath = base_path('resources/views/' . str_replace('.', '/', $page->view_file) . '.json');
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('sync_error', 'Template JSON file not found: ' . $page->view_file);
        }

        $template     = json_decode(file_get_contents($templatePath));
        $campaignJson = json_decode($campaign->json_data);

        if (!$template || !$campaignJson) {
            return redirect()->back()->with('sync_error', 'Failed to parse JSON data.');
        }

        // Replace edit_sections entirely so all new field definitions appear
        if (isset($template->edit_sections)) {
            $campaignJson->edit_sections = $template->edit_sections;
        }

        // Deep-merge sections: keep existing filled values, add new blank slots from template
        if (isset($template->sections)) {
            $campaignJson->sections = isset($campaignJson->sections)
                ? $this->deepMergeTemplate($template->sections, $campaignJson->sections)
                : $template->sections;
        }

        $campaign->json_data = json_encode($campaignJson);
        $campaign->save();

        return redirect()->back()->with('status', 'Template synced successfully. All new fields are now available.');
    }

    public function landingPageView($id)
    {
        $page = LandingPage::find($id);
        if (!$page) {
            abort(404);
        }

        $view_file = $page->view_file;


        // $json = json_decode($page->json_data); // object
        $fileDir= base_path('resources/views/'.str_replace('.', '/', $view_file).'.json');
        $json = json_decode(file_get_contents($fileDir)); // object

        $data = $json->sections ?? [];
        // dd($data);

        $array = (array) $data->products;
        $delivery_areas = delivery_areas::all();
        $productids = array_map(function ($item) {
            return $item->id;
        }, $array ?? []);


        $products = products::whereIn('id', $productids)->get();
        $finalProducts = collect();

        // First, build a map of product info from JSON for easy lookup
        $productJsonMap = [];
        foreach ($data->products ?? [] as $item) {
            $productJsonMap[$item->id] = [
                'order' => $item->order ?? 0,
                'is_primary' => $item->is_primary ?? false,
            ];
        }

        // Loop through DB products and merge JSON info
        foreach ($products as $product) {
            if (isset($productJsonMap[$product->id])) {
                $product->order = $productJsonMap[$product->id]['order'];
                $product->is_primary = $productJsonMap[$product->id]['is_primary'];
                $finalProducts->push($product);
            }
        }

        // Sort by order
        $finalProducts = $finalProducts->sortBy('order')->values();

        // Check result
        // dd($finalProducts);
        $selected_products = $finalProducts->where('is_primary', true)->values();
                $related_products = products::where('status', 1)->where('stock_status', '!=', 'out_of_stock')->whereNotIn('id', $selected_products->pluck('id')->toArray())->inRandomOrder()->limit(8)->get();


        return view($view_file, compact('data', 'delivery_areas', 'finalProducts', 'selected_products', 'related_products'));
    }


}
