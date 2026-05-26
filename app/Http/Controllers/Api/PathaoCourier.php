<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PathaoCourier extends Controller
{
    function webhook(Request $request)
    {
        Log::info('Pathao Webhook Received', [
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);
        if (
            $request->header('x-pathao-signature')
            !== config('services.pathao.webhook_secret')
        ) {
            abort(403);
        }
        /**
         * ------------------------------------------------
         * Pathao integration verification event
         * ------------------------------------------------
         */
        if ($request->event === 'webhook_integration') {

            return response()->json([
                'message' => 'Webhook integrated'
            ], 202)->header(
                'X-Pathao-Merchant-Webhook-Integration-Secret',
                'f3992ecc-59da-4cbe-a049-a13da2018d51'
            );
        }
           

        /**
         * ------------------------------------------------
         * Real webhook events
         * ------------------------------------------------
         */

        $order = Order::find($request->merchant_order_id);

        if (!$order) {

            Log::warning('Order not found', [
                'merchant_order_id' => $request->merchant_order_id
            ]);

            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        /**
         * ------------------------------------------------
         * Save webhook history
         * ------------------------------------------------
         */

        $jsonData = is_array($order->json_data) ? $order->json_data : [];

        $jsonData['pathao_webhooks'][] = [
            'timestamp' => now()->toDateTimeString(),
            'event' => $request->event,
            'payload' => $request->all(),
        ];

        $order->json_data = $jsonData;

        /**
         * ------------------------------------------------
         * Update order status
         * ------------------------------------------------
         */

        switch ($request->event) {

            case 'order.delivered':
                $order->status = 'delivered';
                break;
            case 'order.in-transit':
                $order->status = 'in_transit';
                break;

            case 'order.picked':
                $order->status = 'in_transit';
                break;

            case 'order.cancelled':
                $order->status = 'cancelled';
                break;
          
            case 'order.paid-return':
                $order->status = 'returned';
                break;

            case 'order.returned':
                $order->status = 'returned';
                break;
        }

        /**
         * Optional extra fields
         */

        $order->save();

        return response()->json([
            'success' => true,
        ], 202)->header(
            'X-Pathao-Merchant-Webhook-Integration-Secret',
            'f3992ecc-59da-4cbe-a049-a13da2018d51'
        );
    }
}
