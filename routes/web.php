<?php

use App\Http\Controllers\Admin\StickerController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\TrackingSettingsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SegmentController;
use App\Http\Controllers\Admin\CartController as AdminCartController;
use App\Http\Controllers\CartControllerTest;
use App\Http\Controllers\SessionRecordController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\PathaoCourier;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\AuthAdmin;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Website\CapiController;
use App\Http\Controllers\Website\ProductController as WebsiteProductController;


Route::post('/cart/add/json', [CartController::class, 'add_json_to_cart'])->name('cart.add.json')->withoutMiddleware('auth');

Auth::routes();
// Route::get('/', function () {
//     // return redirect()->away('https://seldomfashion.com/');
//     return view('home');

// });

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');

// Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
//     Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
// });
Route::post('/record-session', [SessionRecordController::class, 'store']);
Route::get('/product/test/1', [HomeController::class, 'ProductTest'])->name('product.test');



// Route::get('/product/flower-silk-3-piece-dress', [HomeController::class,'ProductOne'])->name('product.one');
// Route::get('/product/safina-3-piece-dress', [HomeController::class,'ProductTwo'])->name('product.two');
// Route::get('/product/aafreen-3-piece-dress', [HomeController::class,'ProductThree'])->name('product.three');
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/category/{slug}', [HomeController::class, 'categoryShow'])->name('category.show');
Route::get('/category/{slug}/{subslug}', [HomeController::class, 'SubcategoryShow'])->name('subcategory.show');
Route::get('/{segment}/product/{slug}', [WebsiteProductController::class, 'productShow'])->name('product.show');

//cart
Route::get('/cart/distroy', [CartController::class, 'cart_distroy'])->name('cart.distroy');
Route::get('/cart/test', [CartController::class, 'test'])->name('cart.test');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add_to_cart'])->name('cart.add');

Route::delete('/cart/remove/{id}', [CartController::class, 'remove_item'])->name('cart.remove');
Route::put('/cart/increase/{rowId}', [CartController::class, 'increase_quantity'])->name('cart.increase');
Route::put('/cart/decrease/{rowId}', [CartController::class, 'decrease_quantity'])->name('cart.decrease');
Route::delete('/cart/clear', [CartController::class, 'clear_cart'])->name('cart.clear');
Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/cart/checkout/apply-coupon', [CartController::class, 'apply_coupon'])->name('cart.checkout.apply.coupon');
Route::get('/cart/checkout/remove-coupon', [CartController::class, 'remove_coupon'])->name('cart.checkout.remove.coupon');
Route::post('/cart/checkout/place-order', [CartController::class, 'place_order'])->name('cart.order.place');
Route::get('/order-received', [CartController::class, 'order_received'])->name('order.received')->withoutMiddleware('auth');
Route::get('/order-received-custom/{id}', [CartController::class, 'order_received_custom'])->name('order.received_custom')->middleware('auth');
Route::post('/cart/autosave', [CartController::class, 'orderAutosave'])->name('cart.order.autosave');
Route::post('/cart/autosave/checkout', [CartController::class, 'orderAutosaveCheckout'])->name('cart.order.autosave.checkout');
Route::post('/cart/ordernow', [CartController::class, 'orderNow'])->name('cart.order.now');
Route::post('/cart/landing-order', [CartController::class, 'landingOrder'])->name('cart.landing.order');
Route::post('/cart/purchase', [CartController::class, 'Purchase'])->name('cart.order.purchase');


//cart
Route::get('/cart/view', [CartController::class, 'view'])->name('cart.view');
Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::get('/cart/checkout/place-order', [CartController::class, 'place_order'])->name('cart.checkout.order.place');

Route::post('/device/register', [HomeController::class, 'deviceRegister'])->name('device.register');

Route::post('/fb-pixel-capi', [CapiController::class, 'fbPixelCAPI'])->name('pixel.capi.track');
Route::get('/test', [TestController::class, 'index'])->name('test');

//CAMPAIGN
Route::get('/campaigns', [HomeController::class, 'campaigns'])->name('campaigns');
Route::get('/campaigns/{slug}', [HomeController::class, 'campaign'])->name('campaign.details');

// Admin
Route::prefix('admin')->group(function () {
    Route::middleware(['auth', AuthAdmin::class])->group(function () {
        Route::get('/optimize', function () {
            Artisan::call('optimize:clear');
            Artisan::call('optimize');
            return '<h3>✅ Application optimized successfully!</h3>';
        });
        Route::get('/gitpull', function () {
            $current_path = getcwd();
            $cmd = 'cd ../ && git pull origin main 2>&1';
            $path= getcwd();
            $output = shell_exec($cmd);
            return '<pre>  Current path: ' . e($current_path) .'<br>root path: '.e($output). '<br>' . e($output) . '</pre>';
        });
        Route::get('/debug', function () {
            $mode = request()->query('mode');
            if ($mode == 'on') {
                request()->session()->put('debugmode', true);
            } else {
                request()->session()->put('debugmode', false);
            }
            return response()->json([
                'debug-mode' => request()->session()->get('debugmode'),
            ]);
        });
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.index');

        // Brands
        Route::get('/brands', [AdminController::class, 'brands'])->name('admin.brands');
        Route::get('/brands/add', [AdminController::class, 'brandsAdd'])->name('admin.brands.add');
        Route::post('/brands/store', [AdminController::class, 'brandStore'])->name('admin.brands.store');
        Route::get('/brands/edit/{id}', [AdminController::class, 'brandEdit'])->name('admin.brands.edit');
        Route::post('/brands/update', [AdminController::class, 'brandUpdate'])->name('admin.brands.update');
        Route::delete('/brands/{id}/delete', [AdminController::class, 'brandDelete'])->name('admin.brands.delete');
        // Segments
        Route::get('/segments', [SegmentController::class, 'index'])->name('admin.segments');
        Route::get('/segments/add', [SegmentController::class, 'add'])->name('admin.segments.add');
        Route::post('/segments/store', [SegmentController::class, 'store'])->name('admin.segments.store');
        Route::get('/segments/{id}/edit', [SegmentController::class, 'edit'])->name('admin.segments.edit');
        Route::put('/segments/{id}/update', [SegmentController::class, 'update'])->name('admin.segments.update');
        Route::delete('/segments/{id}/delete', [SegmentController::class, 'delete'])->name('admin.segments.delete');
        Route::get('/segments/{id}/products', [SegmentController::class, 'manageRelation'])->name('admin.segments.products');
        Route::post('/segments/{id}/products', [SegmentController::class, 'assignProducts'])->name('admin.segments.products.assign');
        Route::delete('/segments/{id}/products', [SegmentController::class, 'unassignProducts'])->name('admin.segments.products.unassign');

        // Categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories');
        Route::get('/categories/add', [CategoryController::class, 'add'])->name('admin.categories.add');
        Route::post('/categories/store', [CategoryController::class, 'store'])->name('admin.categories.store');
        Route::get('/categories/edit/{id}', [CategoryController::class, 'edit'])->name('admin.categories.edit');
        Route::post('/categories/update', [CategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{id}/delete', [CategoryController::class, 'delete'])->name('admin.categories.delete');
        Route::delete('/categories/{id}/remove-image', [CategoryController::class, 'removeImage'])->name('admin.categories.image.remove');
        Route::get('/categories/{id}/manage-products', [CategoryController::class, 'manageProducts'])->name('admin.categories.manage.products');
        Route::post('/categories/{id}/assign-products', [CategoryController::class, 'assignProducts'])->name('admin.categories.assign.products');
        Route::delete('/categories/{id}/unassign-products', [CategoryController::class, 'unassignProducts'])->name('admin.categories.unassign.products');

        // Products
        Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
        Route::get('/products/add', [AdminController::class, 'productsAdd'])->name('admin.products.add');
        Route::post('/products/store', [AdminController::class, 'productStore'])->name('admin.products.store');
        Route::get('/products/edit/{id}', [AdminController::class, 'productEdit'])->name('admin.products.edit');
        Route::put('/products/update', [AdminController::class, 'productUpdate'])->name('admin.products.update');
        Route::delete('/products/{id}/delete', [AdminController::class, 'productDelete'])->name('admin.products.delete');
        Route::get('products/add/{id}', [AdminController::class, 'productsAdd'])->name('admin.products.copy');

        // Coupons
        Route::get('/coupons', [AdminController::class, 'coupons'])->name('admin.coupons');
        Route::get('/coupons/add', [AdminController::class, 'couponAdd'])->name('admin.coupons.add');
        Route::post('/coupons/store', [AdminController::class, 'couponStore'])->name('admin.coupons.store');
        Route::get('/coupons/edit/{id}', [AdminController::class, 'couponEdit'])->name('admin.coupons.edit');
        Route::put('/coupons/update/{id}', [AdminController::class, 'couponUpdate'])->name('admin.coupons.update');
        Route::delete('/coupons/{id}/delete', [AdminController::class, 'couponDelete'])->name('admin.coupons.delete');

        // Orders
        Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
        Route::get('/order-drafts', [AdminController::class, 'orderDrafts'])->name('admin.order.drafts');
        Route::get('/orders/{id}/details', [AdminController::class, 'orderDetails'])->name('admin.orders.details');
        Route::put('/orders/update-bulk-order-status', [AdminController::class, 'bulkOrderStatusUpdate'])->name('admin.orders.status.update.bulk');
        Route::put('/orders/{id}/update-status', [AdminController::class, 'orderStatusUpdate'])->name('admin.orders.update');
        Route::delete('/orders/{id}/delete', [AdminController::class, 'deleteOrder'])->name('admin.orders.delete');
        Route::get('/orders/soft-delete/{id}', [AdminController::class, 'ordersoftdelete'])->name('admin.orders.delete.soft');
        Route::get('/orders/export', [AdminController::class, 'exportOrders'])->name('admin.orders.export');
        Route::put('/orders/update/{id}', [AdminController::class, 'updateOrder'])->name('admin.orders.editupdate');
        Route::put('/orders/update/{id}/details', [AdminController::class, 'updateOrderDetails'])->name('admin.orders.update.details');
        Route::get('/orders/add', [AdminController::class, 'orderAdd'])->name('admin.orders.add');
        Route::post('/orders/store', [AdminController::class, 'orderStore'])->name('admin.orders.store');
        Route::get('/carts', [AdminCartController::class, 'index'])->name('admin.carts');
        Route::get('/carts/{cart}', [AdminCartController::class, 'show'])->name('admin.carts.show');

        //delivery areas
        Route::get('/delivery-areas', [AdminController::class, 'deliveryAreas'])->name('admin.deliveryareas');
        Route::get('/delivery-areas/add', [AdminController::class, 'deliveryAreaAdd'])->name('admin.deliveryareas.add');
        Route::post('/delivery-areas/store', [AdminController::class, 'deliveryAreaStore'])->name('admin.deliveryareas.store');
        Route::get('/delivery-areas/edit/{id}', [AdminController::class, 'deliveryAreaEdit'])->name('admin.deliveryareas.edit');
        Route::put('/delivery-areas/update', [AdminController::class, 'deliveryAreaUpdate'])->name('admin.deliveryareas.update');
        Route::delete('/delivery-areas/{id}/delete', [AdminController::class, 'deliveryAreaDelete'])->name('admin.deliveryareas.delete');

        // Slides
        Route::get('/slides', [AdminController::class, 'slides'])->name('admin.slides');
        Route::get('/slides/add', [AdminController::class, 'slideAdd'])->name('admin.slides.add');
        Route::post('/slides/store', [AdminController::class, 'slideStore'])->name('admin.slides.store');
        Route::get('/slides/{id}/edit', [AdminController::class, 'slideEdit'])->name('admin.slides.edit');
        Route::put('/slides/{id}/update', [AdminController::class, 'slideUpdate'])->name('admin.slides.update');
        Route::delete('/slides/{id}/delete', [AdminController::class, 'slideDelete'])->name('admin.slides.delete');

        //Analytics
        Route::get('/analytics/report', [AdminController::class, 'analytics'])->name('admin.analytics.report');
        Route::get('/google-analytics', [AdminController::class, 'gAnalaytics'])->name('admin.google.analytics');
        Route::put('/google-analytics/update', [AdminController::class, 'gAnalyticsUpdate'])->name('admin.google.analytics.update');
        Route::get('/facebook-pixels', [AdminController::class, 'fbPixels'])->name('admin.facebook.pixels');
        Route::put('/facebook-pixels/update', [AdminController::class, 'fbPixelsUpdate'])->name('admin.facebook.pixels.update');
        Route::get('/session-replays', [SessionRecordController::class, 'index'])->name('admin.session.replays');
        Route::get('/session-replays/{id}', [SessionRecordController::class, 'show'])->name('admin.session.replays.show');

        // Customers
        Route::get('/customers', [AdminController::class, 'customers'])->name('admin.customers');
        Route::get('/customers/{id}', [AdminController::class, 'customerDetails'])->name('admin.customers.details');
        Route::get('/customers/{id}/delete', [AdminController::class, 'customerDelete'])->name('admin.customers.delete');
        Route::put('/customers/{id}/update', [AdminController::class, 'updateCustomer'])->name('admin.customers.update');
        Route::get('/orders/{id}/customer-create', [AdminController::class, 'customerCreateFromOrder'])->name('admin.orders.customer.create');

        //Devices
        Route::get('/devices', [AdminController::class, 'devices'])->name('admin.devices');
        Route::get('/devices/{id}', [AdminController::class, 'deviceDetails'])->name('admin.devices.details');
        Route::delete('/devices/{id}/delete', [AdminController::class, 'deviceDelete'])->name('admin.devices.delete');

        //areas
        Route::get('/locations', [AdminController::class, 'locations'])->name('admin.locations');
        Route::post('/location/store', [AdminController::class, 'locationStore'])->name('admin.location.store');
        Route::put('/location/update', [AdminController::class, 'locationUpdate'])->name('admin.location.update');
        Route::delete('/location/delete', [AdminController::class, 'locationDelete'])->name('admin.location.delete');

        // Site Settings
        Route::get('/site-settings', [SiteSettingsController::class, 'index'])->name('admin.site.settings');
        Route::post('/site-settings', [SiteSettingsController::class, 'update'])->name('admin.site.settings.update');

        // Tracking Settings
        Route::get('/tracking-settings', [TrackingSettingsController::class, 'index'])->name('admin.tracking.settings');
        Route::put('/tracking-settings', [TrackingSettingsController::class, 'update'])->name('admin.tracking.settings.update');

        //sticker
        Route::post('/generate-sticker', [StickerController::class, 'generate'])->name('admin.generate.sticker');

        //Campaign
        Route::get('/campaigns', [AdminController::class, 'campaigns'])->name('admin.campaigns');
        Route::get('/campaigns/add', [AdminController::class, 'campaignAdd'])->name('admin.campaigns.add');
        Route::post('/campaigns/store', [AdminController::class, 'campaignStore'])->name('admin.campaigns.store');
        Route::get('/campaigns/{id}/edit', [AdminController::class, 'campaignEdit'])->name('admin.campaigns.edit');
        Route::put('/campaigns/{id}/update', [AdminController::class, 'campaignUpdate'])->name('admin.campaigns.update');
        Route::get('/campaigns/landing-page/{id}/edit', [AdminController::class, 'campaignLandingPageEdit'])->name('admin.campaigns.landingpage.edit');
        Route::put('/campaigns/landing-page/{id}/update', [AdminController::class, 'campaignLandingPageUpdate'])->name('admin.campaigns.landingpage.update');
        Route::delete('/campaigns/{id}/delete', [AdminController::class, 'campaignDelete'])->name('admin.campaigns.delete');
        Route::post('/campaigns/{id}/copy', [AdminController::class, 'campaignCopy'])->name('admin.campaigns.copy');
        Route::post('/campaigns/{id}/toggle-status', [AdminController::class, 'campaignToggleStatus'])->name('admin.campaigns.toggle-status');
        Route::post('/campaigns/landing-page/{id}/sync-template', [AdminController::class, 'campaignSyncTemplate'])->name('admin.campaigns.landingpage.sync');

        //Landing page
        Route::get('/landing-pages/view/{id}', [AdminController::class, 'landingPageView'])->name('admin.landingpages.view');

        // Users
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');

        // Media Library
        Route::get('/media', fn() => view('admin.media.index'))->name('admin.media.index');

        // Media
        Route::delete('/products/media/{id}', [AdminController::class, 'deleteProductMedia'])->name('admin.products.media.delete');

        // Account
        Route::get('/users/account', [AdminController::class, 'account'])->name('admin.account');

        //Settings
        Route::get('/settings/terminal', [AdminController::class, 'terminal'])->name('admin.settings.terminal');

        // Draft cleanup
        Route::post('/order-drafts/cleanup', [AdminController::class, 'draftCleanup'])->name('admin.order.drafts.cleanup');
        });
});
