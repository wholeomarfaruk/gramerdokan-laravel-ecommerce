<?php

namespace App\Http\Controllers;

use App\CAPI\PageViewEvent;
use App\CAPI\PurchaseEvent;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $master_dl= json_decode(request()->cookie('master_dl'), true);
        $event = new PageViewEvent();
        $event->push();
        $payload= $event->payload();
        return response()->json([
            'mester_dl' => null,
            'fbp'=> request()->cookie('_sfdid'),
            'fbc'=> request()->get('_sfud'),
            'payload' => $payload
        ]);
    }
}
