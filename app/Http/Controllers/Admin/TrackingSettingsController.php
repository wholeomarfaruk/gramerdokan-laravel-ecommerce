<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\EnvWriter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TrackingSettingsController extends Controller
{
    public function index()
    {
        $settings = [
            // Meta default
            'META_PIXEL_ID'          => config('conversionapi.meta_pixel_id'),
            'META_ACCESS_TOKEN'      => config('conversionapi.meta_access_token'),
            'META_TEST_EVENT_CODE'   => config('conversionapi.meta_test_code'),
            // GTM
            'GOOGLE_TAG_MANAGER_ID' => config('conversionapi.gtm_id'),
            // TikTok
            'TIKTOK_PIXEL_ID' => config('conversionapi.tiktok_pixel_id'),
            // Queue
            'META_CAPI_QUEUE' => config('conversionapi.meta_capi_queue'),
        ];

        return view('admin.tracking-settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'META_PIXEL_ID'               => 'nullable|string|max:100',
            'META_ACCESS_TOKEN'           => 'nullable|string|max:500',
            'META_TEST_EVENT_CODE'        => 'nullable|string|max:50',
            'GOOGLE_TAG_MANAGER_ID'       => 'nullable|string|max:50',
            'TIKTOK_PIXEL_ID'             => 'nullable|string|max:100',
        ]);

        EnvWriter::set([
            'META_PIXEL_ID'               => $request->input('META_PIXEL_ID', ''),
            'META_ACCESS_TOKEN'           => $request->input('META_ACCESS_TOKEN', ''),
            'META_TEST_EVENT_CODE'        => $request->input('META_TEST_EVENT_CODE', ''),
            'GOOGLE_TAG_MANAGER_ID'       => $request->input('GOOGLE_TAG_MANAGER_ID', ''),
            'TIKTOK_PIXEL_ID'             => $request->input('TIKTOK_PIXEL_ID', ''),
        ]);

        return redirect()->route('admin.tracking.settings')->with('success', 'Tracking settings saved successfully.');
    }
}
