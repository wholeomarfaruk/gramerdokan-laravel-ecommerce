<?php

return [

    // ─── Meta / Facebook (Default) ────────────────────────────────────────────
    'meta_pixel_id'      => env('META_PIXEL_ID'),
    'meta_access_token'  => env('META_ACCESS_TOKEN'),
    'meta_test_code'     => env('META_TEST_EVENT_CODE'),

    // ─── Google Tag Manager ───────────────────────────────────────────────────
    'gtm_id' => env('GOOGLE_TAG_MANAGER_ID'),

    // ─── TikTok ───────────────────────────────────────────────────────────────
    'tiktok_pixel_id' => env('TIKTOK_PIXEL_ID'),

    // ─── Queue ────────────────────────────────────────────────────────────────
    'meta_capi_queue' => env('META_CAPI_QUEUE', 'metacapi'),
];
