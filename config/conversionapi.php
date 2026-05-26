<?php

return [
    /**
     * The access token used by the Conversions API.
     */
    'meta_access_token' => env('META_ACCESS_TOKEN'),

    /**
     * The pixel ID used by the Conversions API.
     */
    'meta_pixel_id' => env('META_PIXEL_ID'),

    /**
     * The Google Tag Manager container ID used in case you're deduplicating
     * events through Google Tag Manager instead of Facebook Pixel directly.
     * Should look something like "GTM-XXXXXX".
     */
    'gtm_id' => env('GOOGLE_TAG_MANAGER_ID'),

    /**
     * The Conversions API comes with a nice way to test your events.
     * You may use this config variable to set your test code.
     */
    'meta_test_code' => env('META_TEST_EVENT_CODE'),
];
