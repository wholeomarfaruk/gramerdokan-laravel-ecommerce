<?php

namespace App\Http\Controllers;

use App\CAPI\PageViewEvent;
use App\CAPI\ViewItemEvent;
use App\Jobs\SendMetaCapiEventJob;
use App\Meta\MetaBaseData;
use App\Models\AreaKeyword;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\delivery_areas;
use App\Models\Slide;
use App\Models\Analytic;
use App\Models\Order;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use stdClass;
use App\Meta\PurchaseEvent;
use App\Support\DataLayer;


class HomeController extends Controller
{
    protected $pixelId;
    protected $accessToken;
    protected $test_event_code;
    protected $pixel_debug_mode = false;
    protected $client;

    public function __construct()
    {
        $this->pixelId         = config('conversionapi.meta_pixel_id');
        $this->accessToken     = config('conversionapi.meta_access_token');
        $this->test_event_code = config('conversionapi.meta_test_code');
    }

    public function index()
    {

        $products = Products::where('status', 1)->orderByDesc('featured') // featured first
            ->orderByDesc('created_at')               // newest first
            ->paginate(12);
        $deliveryAreas = delivery_areas::all();
        $slides = Slide::all();
        $analytics = Analytic::all();
        $categories = Category::where('is_active', true)->orderBy('display_order')->get();
        $homepage_categories = Category::where('is_active', true)
            ->where('homepage_category', true)
            ->orderBy('display_order')
            ->get();
        if (request()->boolean('debug_meta')) {
            $metaBaseData = new MetaBaseData();

            return response()->json([
                'debug' => true,
                'route' => 'home',
                'request' => [
                    'url' => request()->fullUrl(),
                    'ip' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                    'cookies' => [
                        '_fbc' => request()->cookie('_fbc'),
                        '_fbp' => request()->cookie('_fbp'),
                        '_sfdid' => request()->cookie('_sfdid'),
                        '_sfud' => request()->cookie('_sfud'),
                    ],
                ],
                'meta_base_data' => $metaBaseData->toArray(),
            ]);
        }

        return view('home-one', compact('products', 'deliveryAreas', 'slides', 'analytics', 'categories', 'homepage_categories'));
    }
    public function shop()
    {

        $products = Products::where('status', 1)->orderByDesc('featured') // featured first
            ->orderByDesc('created_at')               // newest first
            ->paginate(12);
        $deliveryAreas = delivery_areas::all();
        $slides = Slide::all();
        $analytics = Analytic::all();
        $categories = Category::all();
        return view('shop', compact('products', 'deliveryAreas', 'slides', 'analytics', 'categories'));
    }
    public function categoryShow(Request $request, $slug)
    {

        $category = Category::where('slug', $slug)->first();
        if (!$category) {
            abort(404);
        }

        $products = $category->products()->where('status', 1)->orderByDesc('featured') // featured first
            ->orderByDesc('created_at')               // newest first
            ->paginate(12);
        $segment = $category?->segments?->select('name')?->first() ? strtolower($category->segments->select('name')->first()['name']) : null;
        return view('category-products', compact('category', 'products', 'segment'));
    }
    public function SubcategoryShow(Request $request, $slug, $subslug)
    {
        $category = Category::where('slug', $subslug)->first();
        if (!$category) {
            abort(404);
        }

        $products = $category->products()->orderByDesc('featured') // featured first
            ->orderByDesc('created_at')               // newest first
            ->paginate(12);
        $segment = $category?->segments?->select('name')?->first() ? strtolower($category->segments->select('name')->first()['name']) : null;
        return view('category-products', compact('category', 'products', 'segment'));
    }


    public function productShow(Request $request, $segment, $slug, PageViewEvent $pageViewEvent)
    {


        $product = products::where('slug', $slug)->first();
        if (!$product) {
            abort(404);
        }
        $product->increment('views');

        $deliveryAreas = delivery_areas::limit(5)->get();
        $products = products::where('status', 1)->where('id', '!=', $product->id)->inRandomOrder()->limit(8)->get();
        $segment = $product?->segments?->select('name')?->first() ? strtolower($product->segments->select('name')->first()['name']) : null;
        $capi = new ViewItemEvent();
        $capi->push(
            null,
            currency: 'BDT',
            contentPrice: $product->price,
            contentId: $product->id,
            contentName: $product->name,
            contentType: 'product',
            contentCategory: $segment,
        );
        SendMetaCapiEventJob::dispatch($capi->serverPayload())->onQueue(env('META_CAPI_QUEUE', 'metacapi'));
        $viewItemEventPayload = $capi->browserEventPayload();
        return view('product-show', compact('product', 'deliveryAreas', 'products', 'segment', 'viewItemEventPayload'));
    }
    public function deviceRegister(Request $request)
    {

        // Variable initialization
        $phone = null;
        $name = null;
        $address = null;
        $fn = null;
        $ln = null;
        $udata_cookie = isset($_COOKIE['_sfud']) ? json_decode($_COOKIE['_sfud'], true) : null;
        $device_id_cookie = isset($_COOKIE['_sfdid']) ? $_COOKIE['_sfdid'] : null;
        $customer = null;
        $udata = null;
        $device_id = null;
        $email = null;
        $session_id = session()->getId();
        $session_id_cookie = isset($_COOKIE['_sfid']) ? $_COOKIE['_sfid'] : null;
        $visited_new_customer = "no";
        $visited_old_customer = "no";
        // Get device info
        $deviceInfo = $this->getDeviceInfo($request);
        $deviceInfo = $deviceInfo->getData(); // Extract data from JsonResponse
        $screen_size = $request->input('screen_size', null);
        $data = [
            'user_agent' => $deviceInfo->user_agent,
            'screen_size' => $screen_size,
        ];
        $data = json_encode($data);
        $device_id_hash = hash('sha256', $data);

        //filter number and name address
        if ($request->has('phone') && $request->input('phone') != null) {
            $rawPhone = $request->input('phone');
            // Keep only digits
            $phone = preg_replace('/\D/', '', $rawPhone);
            // Ensure it's last 11 digits
            $phone = substr($phone, -11);
            // If length < 11 and doesn't start with 0, fix it
            if (strlen($phone) === 10 && $phone[0] !== '0') {
                $phone = '0' . $phone;
            }
            if ($request->has('name') && $request->input('name') != null) {
                $rawName = $request->input('name');
                $name = filter_var($rawName, FILTER_SANITIZE_URL);
                $name = filter_var($name, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK);
                $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

                // Remove unwanted characters, allowing letters, spaces, hyphens, and apostrophes
                $name = preg_replace("/[^a-zA-Z-' ]/", '', $rawName);
                // Limit to 50 characters
                $name = substr($name, 0, 150);
                // Capitalize the first letter of each word
                $name = ucwords(strtolower($name));
                // Split into first and last name
                $nameParts = explode(' ', $name);
                $fn = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 0, -1)) : $nameParts[0]; // First name is all but last part
                $ln = count($nameParts) > 1 ? ($nameParts[count($nameParts) - 1]) : null; // Last name is the last part of the array end($nameParts);
            }
            //address
            if ($request->has('address') && $request->input('address') != null) {
                $rawAddress = $request->input('address');
                // Sanitize address
                $address = filter_var($rawAddress, FILTER_SANITIZE_URL);
                $address = filter_var($address, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK);
                $address = htmlspecialchars($address, ENT_QUOTES, 'UTF-8');
                $address = substr($rawAddress, 0, 512);
                // Capitalize the first letter of each word
                $address = ucwords(strtolower($address));
            }

        }
        if ($request->has('email') && $request->input('email') != null) {
            $rawEmail = $request->input('email');
            // Sanitize email
            $email = filter_var($rawEmail, FILTER_SANITIZE_EMAIL);
            // Validate email
            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                $email = null; // Invalid email
            }
        }
        //variable initialization end


        //_sfud Full work process
        //first check  cookie
        if (!$udata_cookie && $phone !== null && Str::length($phone) == 11) {
            $customer = $this->storeCustomer($fn, $ln, $email, $phone, $address);
            $udata = json_encode($customer);
            $visited_new_customer = "yes";
            setcookie('_sfud', $udata, time() + (86400 * 30 * 12), "/", '', true); // HttpOnly and Secure flags
            $device = Device::where('device_id', $device_id_cookie)->first();
            if ($device) {
                $device->customer_id = $customer->id;
                $device->save();
            }
        } elseif ($phone !== null && Str::length($phone) == 11 && $udata_cookie['phone'] == $phone) {

            $visited_old_customer = "yes";
            if (!isset($udata_cookie['first_name']) && $fn !== null) {
                $customer = $this->updateCustomer($fn, null, null, $phone, null);
                $udata = json_encode($customer);
                setcookie('_sfud', $udata, time() + (86400 * 30 * 12), "/", '', true); // HttpOnly and Secure flags

            }
            if (!isset($udata_cookie['last_name']) && $ln !== null) {
                $customer = $this->updateCustomer(null, $ln, null, $phone, null);
                $udata = json_encode($customer);
                setcookie('_sfud', $udata, time() + (86400 * 30 * 12), "/", '', true); // HttpOnly and Secure flags

            }
            if (!isset($udata_cookie['email']) && $email !== null) {
                $customer = $this->updateCustomer(null, null, $email, $phone, null);
                $udata = json_encode($customer);
                setcookie('_sfud', $udata, time() + (86400 * 30 * 12), "/", '', true); // HttpOnly and Secure flags
            }
            if (!isset($udata_cookie['address']) && $address !== null) {
                $customer = $this->updateCustomer(null, null, null, $phone, $address);
                $udata = json_encode($customer);
                setcookie('_sfud', $udata, time() + (86400 * 30 * 12), "/", '', true); // HttpOnly and Secure flags
            }
            if (!$customer) {
                $customer = Customer::where('phone', $phone)->first();
            }
        }
        if (isset($session_id) && $session_id != $session_id_cookie) {
            $device_data = Device::where('device_id', $device_id_cookie)->first();

            if ($device_data) {
                $udata = $device_data->customer;
                setcookie('_sfud', $udata, time() + (86400 * 30 * 12), "/", '', true); // HttpOnly and Secure flags

            }
            setcookie('_sfsid', $session_id, time() + (86400 * 1), "/", '', true); // HttpOnly and Secure flags
        }
        //end _sfud work process

        //device register process start




        if ($device_id_hash !== $device_id_cookie || !$device_id_cookie) {
            $device = Device::where('device_id', $device_id_hash)->first();
            if ($device) {
                if ($customer ?? false) {
                    $device->customer_id = $customer->id;
                }
                $device->last_activity = now();
                $device->save();
                setcookie('_sfdid', $device_id_hash, time() + (86400 * 30 * 12), "/", "", true); // HttpOnly and Secure flags
                return response()->json([
                    'message' => 'Device already registered.',
                    'device_id' => $device_id_hash,
                    'customer' => $customer,
                    'status' => 'exists',
                ]);
            } else {

                $diviceRegister = new Device();
                if ($customer ?? false) {
                    $diviceRegister->customer_id = $customer->id;
                }
                $diviceRegister->device_id = $device_id_hash;
                $diviceRegister->device_type = $deviceInfo->device_type;
                $diviceRegister->device_model = $deviceInfo->device_model;
                $diviceRegister->user_agent = $deviceInfo->user_agent;
                $diviceRegister->screen_size = $request->input('screen_size', null);
                $diviceRegister->last_activity = now();
                $diviceRegister->save();
                setcookie('_sfdid', $device_id_hash, time() + (86400 * 30 * 12), "/", "", true); // Secure flags

            }
        } elseif ($device_id_hash === $device_id_cookie) {
            $device = Device::where('device_id', $device_id_cookie)->first();

            if ($device) {
                if ($customer ?? false) {
                    $device->customer_id = $customer->id;
                }
                $device->last_activity = now();
                $device->save();
            } else {
                $diviceRegister = new Device();
                if ($customer ?? false) {
                    $diviceRegister->customer_id = $customer->id;
                }
                $diviceRegister->device_id = $device_id_hash;
                $diviceRegister->device_type = $deviceInfo->device_type;
                $diviceRegister->device_model = $deviceInfo->device_model;
                $diviceRegister->user_agent = $deviceInfo->user_agent;
                $diviceRegister->screen_size = $request->input('screen_size', null);
                $diviceRegister->last_activity = now();
                $diviceRegister->save();
                setcookie('_sfdid', $device_id_hash, time() + (86400 * 30 * 12), "/", "", true); // Secure flags
                return response()->json([
                    'message' => 'registered successfully in 2nd time.',
                    'device_id' => $device_id_hash,
                    'old_device_id' => $device_id_cookie,
                    'customer' => $customer,
                    'new_customer' => $visited_new_customer,
                    'old_customer' => $visited_old_customer,
                    'status' => 'exists'
                ]);
            }


        }


        return response()->json([
            'message' => 'Device registered successfully.',
            'device_id' => $device_id_hash,
            'customer' => $customer,
            'new_customer' => $visited_new_customer,
            'old_customer' => $visited_old_customer,
            'status' => 'success'
        ]);
    }
    public function getDeviceInfo(Request $request)
    {
        $agent = new Agent();

        // Default Device Type
        $deviceType = 'Desktop';

        if ($agent->isTablet()) {
            $deviceType = 'Tablet';
        } elseif ($agent->isMobile()) {
            $deviceType = 'Mobile';
        }

        // Extra check for Smart TV / Console (not fully supported by Agent, use userAgent)
        $userAgent = $request->header('User-Agent');
        if (stripos($userAgent, 'SMART-TV') !== false || stripos($userAgent, 'HbbTV') !== false) {
            $deviceType = 'TV';
        } elseif (stripos($userAgent, 'Xbox') !== false || stripos($userAgent, 'PlayStation') !== false) {
            $deviceType = 'Console';
        } elseif ($agent->isDesktop()) {
            $deviceType = 'Laptop/Desktop';
        }

        // Device Model (iPhone, Samsung, etc.)
        $deviceModel = $agent->device();

        // Browser/App Name
        $browser = $agent->browser();
        $browserVersion = $agent->version($browser);

        // Detect special in-app browsers (Facebook, Instagram, etc.)
        $appName = $browser;
        if (stripos($userAgent, 'FBAN') !== false || stripos($userAgent, 'FBAV') !== false) {
            $appName = 'Facebook App';
        } elseif (stripos($userAgent, 'Instagram') !== false) {
            $appName = 'Instagram App';
        } elseif (stripos($userAgent, 'WhatsApp') !== false) {
            $appName = 'WhatsApp Browser';
        }

        // Platform (Windows, macOS, Android, iOS, etc.)
        $platform = $agent->platform();
        $platformVersion = $agent->version($platform);

        return response()->json([
            'device_type' => $deviceType,
            'device_model' => $deviceModel,
            'browser_or_app' => $appName,
            'browser_version' => $browserVersion,
            'platform' => $platform,
            'platform_version' => $platformVersion,
            'user_agent' => $userAgent, // for debugging

        ]);
    }

    protected function storeCustomer($fn = null, $ln = null, $email = null, $phone = null, $address = null)
    {
        if ($phone == null) {
            return null;
        }
        $customer = Customer::where('phone', $phone)->first();
        if ($customer) {
            return $customer;
        }
        $customer = new Customer();
        $customer->phone = $phone;
        if ($fn != null && $fn != '') {
            $customer->first_name = $fn;
        }
        if ($ln != null && $ln != '') {
            $customer->last_name = $ln;
        }
        if ($email != null && $email != '') {
            $customer->email = $email;
        }
        if ($address != null && $address != '') {
            $customer->address = $address;
        }
        $customer->save();

        return $customer;
    }
    protected function updateCustomer($fn = null, $ln = null, $email = null, $phone = null, $address = null)
    {
        if ($phone == null) {
            return null;
        }
        $customer = Customer::where('phone', $phone)->first();
        if (!$customer) {
            return null;
        }
        if ($fn != null && $fn != '' && ($customer->first_name == null || empty($customer->first_name))) {
            $customer->first_name = $fn;
        }
        if ($ln != null && $ln != '' && ($customer->last_name == null || empty($customer->last_name))) {
            $customer->last_name = $ln;
        }
        if ($email != null && $email != '' && ($customer->email == null || empty($customer->email))) {
            $customer->email = $email;
        }
        if ($address != null && $address != '' && ($customer->address == null || empty($customer->address))) {
            $customer->address = $address;

        }
        if ($customer->city == null || $customer->state == null || $customer->country == null || $customer->zip_code == null) {
            $this->updateCustomerAddress($customer->id, $customer->address);
        }
        $customer->save();
        return $customer;
    }
    public static function updateCustomerAddress($customer_id, $address)
    {
        $customer = Customer::find($customer_id);
        if (!$customer) {
            return null;
        }

        if ($address == null || $address == '') {
            return null;
        }

        // Get all keywords from area_keywords table
        $keywords = AreaKeyword::pluck('name'); // ['dogair', 'demra', ...]

        // Find the first keyword that exists in the address
        $matchedKeyword = null;
        foreach ($keywords as $keyword) {
            if (stripos($address, $keyword) !== false) {
                $matchedKeyword = $keyword;
                break; // stop at first match
            }
        }

        if ($matchedKeyword) {
            // You can fetch the full area record if needed
            $area = AreaKeyword::where('name', $matchedKeyword)->first();

            // Optionally, save or update something in customer
            $customer->street = $area->name;
            $customer->zip_code = $area->zipcode->code ?? null;
            $customer->city = $area->zipcode->police_station->city->name ?? null;
            $customer->state = $area->zipcode->police_station->city->state->name ?? null;

            $customer->save();

            return $area; // return first matched area
        }

        return null; // no match found
    }
    public function fbPixelCAPI(Request $request)
    {

        // ignore_user_abort(true);
        // set_time_limit(0);

        // // Return early to frontend (optional)
        // response()->json(['status' => 'processing'])->send();
        // if (function_exists('fastcgi_finish_request')) {
        //     fastcgi_finish_request();
        // }

        $data = $request->all();
        // Log::info('FB Pixel CAPI Calling Started at ' . now());
        // Log::info('FB Pixel CAPI Data: ' . json_encode($data));
        try {
            $filterData = ['men', 'women', 'kids'];
            $segment = null;

            $segment = $request->segment ? strtolower($request->segment) : null;

            if ($request->event_name == 'page_view') {

                // $payload = [
                //     'data' => [
                //         [
                //             'event_name' => 'PageView',
                //             'action_source' => 'website',
                //             'event_time' => time(),
                //             'event_id' => $data['event_id'] ?? (string) Str::uuid(),
                //             'event_source_url' => !empty($data['event_source_url']) ? $data['event_source_url'] : $request->url(),
                //             'referrer_url' => !empty($data['referrer_url']) ? $data['referrer_url'] : $request->headers->get('referer'),
                //             'custom_data' => new stdClass,
                //             'user_data' => [
                //                 'client_user_agent' => $request->server('HTTP_USER_AGENT'),
                //                 'client_ip_address' => $request->ip(),
                //                 'fbp' => isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : null,
                //                 'fbc' => !empty($data['user_data']['fbc']) ? $data['user_data']['fbc'] : (isset($_COOKIE['custom_fbc']) ? $_COOKIE['custom_fbc'] : null),
                //                 'ph' => !empty($data['user_data']['phone_number']) ? $this->normalizeAndHash($data['user_data']['phone_number']) : null,
                //                 'fn' => !empty($data['user_data']['first_name']) ? $this->normalizeAndHash(strtolower(trim($data['user_data']['first_name']))) : null,
                //                 'ln' => !empty($data['user_data']['last_name']) ? $this->normalizeAndHash(strtolower(trim($data['user_data']['last_name']))) : null,
                //                 'external_id' => !empty($data['user_data']['customer_id']) ? $this->normalizeAndHash(strtolower(trim($data['user_data']['customer_id']))) : null,
                //                 'country' => $this->normalizeAndHash('bd'),
                //                 'st' => $this->normalizeAndHash($data['user_data']['state'] ?? null),
                //                 'ct' => $this->normalizeAndHash($data['user_data']['city'] ?? null),
                //                 'zp' => $this->normalizeAndHash($data['user_data']['zipcode'] ?? null),
                //             ],

                //         ],
                //     ],
                //     // 'test_event_code' => $this->test_event_code,
                // ];

                // $payload = json_decode(json_encode($payload, JSON_PRETTY_PRINT), true);
                // $client = new Client([
                //     'base_uri' => 'https://graph.facebook.com/v23.0/',
                // ]);
                // $response = $client->post($this->pixelId . '/events', [
                //     'query' => [
                //         'access_token' => $this->accessToken,
                //     ],
                //     'json' => $payload,
                // ]);



                // return response()->json([
                //     'status' => 'success',
                //     'data' => $data,
                //     'request' => $payload,
                //     'pixelData' => "Segment: " . $segment . " pixel id: " . $this->pixelId . " token:" . $this->accessToken,
                //     'response' => json_decode($response->getBody(), true)
                // ], 200);


                $pageViewEvent = new PageViewEvent();
                $pageViewEvent->push();
                $payload = $pageViewEvent->payload();
                SendMetaCapiEventJob::dispatch($payload)
                    ->onQueue(env('META_CAPI_QUEUE', 'metacapi'));
                // $response = $pageViewEvent->sendServerSide();
                return response()->json([
                    'status' => 'success',
                    'data' => $data,
                    'request' => $payload
                ], 200);
            }
            // Log::info('FB Pixel CAPI after pageView Started at ' . now());

            if ($request->event_name == 'view_content') {
                // Handle ViewContent event similarly
                $contents = [];
                if (isset($data['ecommerce']['items']) && count($data['ecommerce']['items']) > 0) {
                    foreach ($data['ecommerce']['items'] as $item) {
                        $contents[] = [
                            'id' => $item['item_id'] ? (int) $item['item_id'] : null,
                            'quantity' => $item['quantity'] ?? 1,
                            'item_price' => $item['price'] ?? null,
                        ];
                    }
                }

                $payload = [
                    'data' => [
                        [
                            'event_name' => 'ViewContent',
                            'action_source' => 'website',
                            'event_time' => time(),
                            'event_id' => $data['event_id'] ?? (string) Str::uuid(),
                            'event_source_url' => !empty($data['event_source_url']) ? $data['event_source_url'] : $request->url(),
                            'referrer_url' => !empty($data['referrer_url']) ? $data['referrer_url'] : $request->headers->get('referer'),

                            'custom_data' => [
                                'currency' => 'BDT',
                                'value' => $data['ecommerce']['value'] ?? null,
                                'contents' => $contents,
                                'category' => $segment,
                            ],
                            'user_data' => [
                                'client_user_agent' => $request->server('HTTP_USER_AGENT'),
                                'client_ip_address' => $request->ip(),
                                'fbp' => isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : null,
                                'fbc' => !empty($data['user_data']['fbc']) ? $data['user_data']['fbc'] : (isset($_COOKIE['custom_fbc']) ? $_COOKIE['custom_fbc'] : null),
                                'ph' => !empty($data['user_data']['phone_number']) ? $this->normalizeAndHash($data['user_data']['phone_number']) : null,
                                'fn' => !empty($data['user_data']['first_name']) ? $this->normalizeAndHash(strtolower(trim($data['user_data']['first_name']))) : null,
                                'ln' => !empty($data['user_data']['last_name']) ? $this->normalizeAndHash(strtolower(trim($data['user_data']['last_name']))) : null,
                                'external_id' => !empty($data['user_data']['customer_id']) ? $this->normalizeAndHash(strtolower(trim($data['user_data']['customer_id']))) : null,
                                'country' => $this->normalizeAndHash('bd'),
                                'st' => $this->normalizeAndHash($data['user_data']['state'] ?? null),
                                'ct' => $this->normalizeAndHash($data['user_data']['city'] ?? null),
                                'zp' => $this->normalizeAndHash($data['user_data']['zipcode'] ?? null),
                            ],
                        ],
                    ],
                    // 'test_event_code' => $this->test_event_code,
                ];

                $payload = json_decode(json_encode($payload, JSON_PRETTY_PRINT), true);
                $client = new Client([
                    'base_uri' => 'https://graph.facebook.com/v23.0/',
                ]);
                $response = $client->post($this->pixelId . '/events', [
                    'query' => [
                        'access_token' => $this->accessToken,
                    ],
                    'json' => $payload,
                ]);

                // return response()->json([
                //     'status' => 'success',
                //     'data' => $data,
                //     'request' => $payload,
                //     'pixelData' => $this->pixelId . " token:" . $this->accessToken,
                //     'response' => json_decode($response->getBody(), true)
                // ], 200);
            }
            //   Log::info('FB Pixel CAPI after viewContent Started at ' . now());
            if ($request->event_name == 'initiate_checkout') {
                // Handle ViewContent event similarly

                

                $contents = [];
                if (isset($data['ecommerce']['items']) && count($data['ecommerce']['items']) > 0) {
                    foreach ($data['ecommerce']['items'] as $item) {
                        $contents[] = [
                            'id' => $item['item_id'] ? (int) $item['item_id'] : null,
                            'quantity' => $item['quantity'] ?? 1,
                            'item_price' => $item['price'] ?? null,
                        ];
                    }
                }

                $payload = [
                    'data' => [
                        [
                            'event_name' => 'InitiateCheckout',
                            'action_source' => 'website',
                            'event_time' => time(),
                            'event_id' => $data['event_id'] ?? (string) Str::uuid(),
                            'event_source_url' => !empty($data['event_source_url']) ? $data['event_source_url'] : $request->url(),
                            'referrer_url' => !empty($data['referrer_url']) ? $data['referrer_url'] : $request->headers->get('referer'),

                            'custom_data' => [
                                'currency' => 'BDT',
                                'value' => $data['ecommerce']['value'] ?? null,
                                'contents' => $contents,
                                'category' => $segment,
                            ],
                            'user_data' => [
                                'client_user_agent' => $request->server('HTTP_USER_AGENT'),
                                'client_ip_address' => $request->ip(),
                                'fbp' => isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : null,
                                'fbc' => !empty($data['user_data']['fbc']) ? $data['user_data']['fbc'] : (isset($_COOKIE['custom_fbc']) ? $_COOKIE['custom_fbc'] : null),
                                'ph' => !empty($data['user_data']['phone_number']) ? $this->normalizeAndHash($data['user_data']['phone_number']) : null,
                                'fn' => !empty($data['user_data']['first_name']) ? $this->normalizeAndHash(strtolower(trim($data['user_data']['first_name']))) : null,
                                'ln' => !empty($data['user_data']['last_name']) ? $this->normalizeAndHash(strtolower(trim($data['user_data']['last_name']))) : null,
                                'external_id' => !empty($data['user_data']['customer_id']) ? $this->normalizeAndHash(strtolower(trim($data['user_data']['customer_id']))) : null,
                                'country' => $this->normalizeAndHash('bd'),
                                'st' => $this->normalizeAndHash($data['user_data']['state'] ?? null),
                                'ct' => $this->normalizeAndHash($data['user_data']['city'] ?? null),
                                'zp' => $this->normalizeAndHash($data['user_data']['zipcode'] ?? null),
                            ],
                        ],
                    ],

                    // 'test_event_code' => $this->test_event_code,
                ];

                $payload = json_decode(json_encode($payload, JSON_PRETTY_PRINT), true);
                $client = new Client([
                    'base_uri' => 'https://graph.facebook.com/v23.0/',
                ]);
                $response = $client->post($this->pixelId . '/events', [
                    'query' => [
                        'access_token' => $this->accessToken,
                    ],
                    'json' => $payload,
                ]);

                // return response()->json([
                //     'status' => 'success',
                //     'data' => $data,
                //     'request' => $payload,
                //     'response' => json_decode($response->getBody(), true)
                // ], 200);
            }
            Log::info('FB Pixel CAPI after initiatecheckout Started at ' . now());

            if ($request->event_name == 'purchase') {
                // Handle ViewContent event similarly


                $contents = [];
                if (isset($data['ecommerce']['items']) && count($data['ecommerce']['items']) > 0) {
                    foreach ($data['ecommerce']['items'] as $item) {
                        $contents[] = [
                            'id' => $item['item_id'] ? (int) $item['item_id'] : null,
                            'quantity' => $item['quantity'] ?? 1,
                            'item_price' => $item['price'] ?? null,
                        ];
                    }
                }

                $payload = [
                    'data' => [
                        [
                            'event_name' => 'Purchase',
                            'action_source' => 'website',
                            'event_time' => time(),
                            'event_id' => $data['event_id'] ?? (string) Str::uuid(),
                            'event_source_url' => !empty($data['event_source_url']) ? $data['event_source_url'] : $request->url(),
                            'referrer_url' => !empty($data['referrer_url']) ? $data['referrer_url'] : $request->headers->get('referer'),

                            'custom_data' => [
                                'currency' => 'BDT',
                                'value' => $data['ecommerce']['value'] ?? null,
                                'transaction_id' => $data['ecommerce']['transaction_id'] ?? null,
                                'contents' => $contents,
                                'category' => $segment,
                            ],
                            'user_data' => [
                                'client_user_agent' => $request->server('HTTP_USER_AGENT'),
                                'client_ip_address' => $request->ip(),
                                'fbp' => isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : null,
                                'fbc' => !empty($data['user_data']['fbc']) ? $data['user_data']['fbc'] : (isset($_COOKIE['custom_fbc']) ? $_COOKIE['custom_fbc'] : null),
                                'ph' => !empty($data['user_data']['phone_number']) ? $this->normalizeAndHash($data['user_data']['phone_number']) : null,
                                'fn' => !empty($data['user_data']['first_name']) ? $this->normalizeAndHash(strtolower(trim($data['user_data']['first_name']))) : null,
                                'ln' => !empty($data['user_data']['last_name']) ? $this->normalizeAndHash(strtolower(trim($data['user_data']['last_name']))) : null,
                                'external_id' => !empty($data['user_data']['customer_id']) ? $this->normalizeAndHash(strtolower(trim($data['user_data']['customer_id']))) : null,
                                'country' => $this->normalizeAndHash('bd'),
                                'st' => $this->normalizeAndHash($data['user_data']['state'] ?? null),
                                'ct' => $this->normalizeAndHash($data['user_data']['city'] ?? null),
                                'zp' => $this->normalizeAndHash($data['user_data']['zipcode'] ?? null),
                            ],
                        ],
                    ],
                    // 'test_event_code' => $this->test_event_code,
                ];

                $payload = json_decode(json_encode($payload, JSON_PRETTY_PRINT), true);
                $client = new Client([
                    'base_uri' => 'https://graph.facebook.com/v23.0/',
                ]);
                $response = $client->post($this->pixelId . '/events', [
                    'query' => [
                        'access_token' => $this->accessToken,
                    ],
                    'json' => $payload,
                ]);

                // return response()->json([
                //     'status' => 'success',
                //     'data' => $data,
                //     'request' => $payload,
                //     'response' => json_decode($response->getBody(), true)
                // ], 200);
                $order_id = $data['ecommerce']['transaction_id'];
                if (is_integer($order_id)) {
                    $order = Order::find($order_id);
                    if ($order) {
                        if ($order->trackingEvent) {

                            $order->trackingEvent()->update([
                                'is_fired' => true,
                                'json_data' => $response->getBody()->getContents(),
                                'event_fired_time' => now(),
                            ]);
                        }

                    }
                }


            }
            //   Log::info('FB Pixel CAPI after purchase Started at ' . now());

        } catch (\Exception $e) {
            Log::info('FB Pixel CAPI Calling Ends At: ' . now() . " error: " . $e->getMessage());
        }



        // Log::info('FB Pixel CAPI Calling Ends At: ' . now());

        // return response()->json(['message' => 'FB Pixel CAPI data received'], 200);
    }
    public function customFbPixelCAPI($id)
    {
        return response()->json([
            'status' => 'stopped',
        ]);
        $order = Order::findOrFail($id);
        $trackingEvent = $order->trackingEvent;
        $customer = $order->customer;
        if (!$trackingEvent) {
            return redirect()->back()->with('error', 'Tracking event not found');
        }

        $segment = $trackingEvent->segment;




        try {
            $filterData = ['men', 'women', 'kids'];

            if ($trackingEvent->segment) {
                $segment = strtolower($trackingEvent->segment);

                foreach ($filterData as $value) {
                    if (preg_match('/\b' . preg_quote($value, '/') . '\b/', $segment)) { // case-insensitive search, match full exact and full word
                        $segment = $value;
                        break; // stop at first match
                    }
                }
            }

            if ($trackingEvent->event_name == 'purchase') {
                // Handle ViewContent event similarly


                $contents = [];
                if (isset($order->Order_Item) && $order->Order_Item->count() > 0) {
                    foreach ($order->Order_Item as $item) {
                        $contents[] = [
                            'id' => $item->product_id ? (int) $item->product_id : null,
                            'quantity' => $item->quantity ?? 1,
                            'item_price' => $item->price ?? null,
                        ];
                    }
                }

                $payload = [
                    'data' => [
                        [
                            'event_name' => 'Purchase',
                            'action_source' => 'website',
                            'event_time' => time(),
                            'event_id' => $data['event_id'] ?? (string) Str::uuid(),
                            'event_source_url' => $trackingEvent->url ?? null,
                            'referrer_url' => $trackingEvent->referrer ?? null,

                            'custom_data' => [
                                'currency' => 'BDT',
                                'value' => $order->total ?? null,
                                'transaction_id' => $order->id ?? null,
                                'contents' => $contents,
                                'category' => $segment,
                            ],
                            'user_data' => [
                                'client_user_agent' => $trackingEvent->user_agent ?? null,
                                'client_ip_address' => $trackingEvent->ip_address ?? null,
                                'fbp' => isset($trackingEvent->tud_id) ? $this->normalizeAndHash($trackingEvent->tud_id) : null,
                                'fbc' => $trackingEvent->tracking_id ?? null,
                                'ph' => isset($order->phone) ? $this->normalizeAndHash($order->phone) : null,
                                'fn' => isset($order->name) ? $this->normalizeAndHash(strtolower(trim($order->name))) : null,
                                // 'ln' => !empty($data['user_data']['last_name']) ? $this->normalizeAndHash(strtolower(trim($data['user_data']['last_name']))) : null,
                                'external_id' => isset($customer?->id) ? $this->normalizeAndHash(strtolower(trim($customer->id))) : null,
                                'country' => $this->normalizeAndHash('bd'),
                                'st' => $customer?->state ? $this->normalizeAndHash($customer?->state) : null,
                                'ct' => $customer?->city ? $this->normalizeAndHash($customer?->city) : null,
                                'zp' => $customer?->zip_code ? $this->normalizeAndHash($customer?->zip_code) : null,
                            ],
                        ],
                    ],
                    // 'test_event_code' => $this->test_event_code,
                ];

                $payload = json_decode(json_encode($payload, JSON_PRETTY_PRINT), true);
                $client = new Client([
                    'base_uri' => 'https://graph.facebook.com/v23.0/',
                ]);
                $response = $client->post($this->pixelId . '/events', [
                    'query' => [
                        'access_token' => $this->accessToken,
                    ],
                    'json' => $payload,
                ]);

                return response()->json([
                    'status' => 'success',
                    'request' => $payload,
                    'response' => json_decode($response->getBody(), true)
                ], 200);
            }
            //   Log::info('FB Pixel CAPI after purchase Started at ' . now());

        } catch (\Exception $e) {
            Log::info('FB Pixel CAPI Calling Ends At: ' . now() . " error: " . $e->getMessage());
        }

    }

    public function normalizeAndHash($value)
    {
        if (empty($value))
            return null;               // skip null/empty
        $normalized = strtolower(trim($value));       // lowercase + trim
        return hash('sha256', $normalized);           // hash with SHA-256
    }


    //campaign method
    public function campaign($slug)
    {
        $campaign = Campaign::where('slug', $slug)->first();
        if (!$campaign) {
            abort(404);
        }
        $view_file = $campaign->view_file;


        $json = json_decode($campaign->json_data); // object
        // $json = json_decode(file_get_contents(base_path('resources/views/templates/landingpages/seldom_zaynah_eid.json'))); // object

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

    public function search(Request $request)
    {
        $query = $request->get('search');
        $products = products::where('status', 1)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%');
            })
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('search', compact('products'));
    }

    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }

}
