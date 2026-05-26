<?php

namespace App\Http\Controllers\Website;

use App\CAPI\PageViewEvent;
use App\Http\Controllers\Controller;
use App\Jobs\SendMetaCapiEventJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Order;

class CapiController extends Controller
{
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

        try {
            $filterData = ['men', 'women', 'kids'];
            $segment = null;

            $segment = $request->segment ? strtolower($request->segment) : null;


            if ($request->event_name == 'page_view') {

                $pageViewEvent = new PageViewEvent();
                $pageViewEvent->push();
                $pageViewEvent->set('event_id', (isset($data['event_id']) ? $data['event_id'] : null));
                $payload = $pageViewEvent->payload();
                SendMetaCapiEventJob::dispatch($payload)->onQueue(env('META_CAPI_QUEUE', 'metacapi'));
                return response()->json([
                    'status' => 'success',
                    'data' => $data,
                    'request' => $payload
                ], 200);
            }
            // Log::info('FB Pixel CAPI after pageView Started at ' . now());

            if ($request->event_name == 'view_content') {
                // Handle ViewContent event similarly
            }

            if ($request->event_name == 'initiate_checkout') {
                // Handle InitiateCheckout event similarly

                $payload = $request->payload ?? [];

                if (is_string($payload)) {
                    $payload = json_decode($payload, true);
                }

                if (!is_array($payload)) {
                    $payload = [];
                }

                unset($payload['event'], $payload['gtm.uniqueEventId'], $payload['ecommerce']);
                $payload=[
                    'data' =>[$payload]

                ];

                SendMetaCapiEventJob::dispatch($payload)->onQueue(env('META_CAPI_QUEUE', 'metacapi'));
                return response()->json([
                    'status' => 'success',
                    'data' => $data,
                    'request' => $payload
                ], 200);

            }


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

                ];

                $order_id = $data['ecommerce']['transaction_id'];
                if (is_integer($order_id)) {
                    $order = Order::find($order_id);
                    if ($order) {
                        if ($order->trackingEvent) {

                            $order->trackingEvent()->update([
                                'is_fired' => true,
                                'json_data' => json_encode($payload),
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



}
