<?php

namespace App\Http\Controllers\Website;

use App\CAPI\AddToCartEvent;
use App\CAPI\PageViewEvent;
use App\CAPI\PurchaseEvent;
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
                $pageViewEvent->set('event_id', $data['event_id'] ?? null);
                SendMetaCapiEventJob::dispatch($pageViewEvent->serverPayload())->onQueue(env('META_CAPI_QUEUE', 'metacapi'));
                return response()->json(['status' => 'success'], 200);
            }

            if ($request->event_name == 'add_to_cart') {

                $event = new AddToCartEvent();
                $event->push(
                    eventId:       $data['event_id'] ?? null,
                    currency:      'BDT',
                    contentPrice:  isset($data['value']) ? (float) $data['value'] : null,
                    contentId:     $data['content_id'] ?? null,
                    contentName:   $data['content_name'] ?? null,
                    contentType:   'product',
                    contentCategory: $segment,
                );
                if (!empty($data['quantity'])) {
                    $event->set('contents', [[
                        'id'         => $data['content_id'] ?? null,
                        'quantity'   => (int) $data['quantity'],
                        'item_price' => isset($data['value']) ? (float) $data['value'] : null,
                    ]]);
                }
                SendMetaCapiEventJob::dispatch($event->serverPayload())->onQueue(env('META_CAPI_QUEUE', 'metacapi'));
                return response()->json(['status' => 'success'], 200);
            }

            if ($request->event_name == 'purchase') {

                $contents   = $data['contents'] ?? [];
                $contentIds = array_values(array_filter(array_column($contents, 'id')));
                $event = new PurchaseEvent();
                $event->push(
                    eventId:      $data['event_id'] ?? null,
                    currency:     'BDT',
                    contentPrice: isset($data['value']) ? (float) $data['value'] : null,
                    contentIds:   $contentIds,
                    content_type: 'product',
                    contents:     $contents,
                    order_id:     isset($data['order_id']) ? (string) $data['order_id'] : null,
                );
                SendMetaCapiEventJob::dispatch($event->serverPayload())->onQueue(env('META_CAPI_QUEUE', 'metacapi'));
                return response()->json(['status' => 'success'], 200);
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

        } catch (\Exception $e) {
            Log::info('FB Pixel CAPI Calling Ends At: ' . now() . " error: " . $e->getMessage());
        }



        // Log::info('FB Pixel CAPI Calling Ends At: ' . now());

        // return response()->json(['message' => 'FB Pixel CAPI data received'], 200);
    }



}
