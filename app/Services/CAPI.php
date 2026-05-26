<?php

namespace App\Services;

use GuzzleHttp\Client;

class CAPI
{
    protected $pixelId;
    protected $accessToken;
    protected $client;

    public function __construct()
    {
        // dd(env('FB_PIXEL_ID'), env('FB_ACCESS_TOKEN'));

        $this->pixelId = "2050688985371395";
        $this->accessToken = "EAAWH3RG69LkBPTTsgdYyicYY7zzckWU71l0O9vzHcisQtQzSwpwYK546Cp5v96FY0Kx2f6mgWT3DoSA8rPdpnIZCL6nwnxlKHXdQiyZBDZBof11syObER1nXZC2d6S6wV0Jy6O9DMYPV8W4hkY6ryEu07f807iQVJuxnlWWmor6YqVdlnCL2pODZB18iL8SSoogZDZD";
        $this->client = new Client([
            'base_uri' => 'https://graph.facebook.com/v18.0/',
        ]);
    }

    public function sendEvent($eventName, $eventId, $userData, $customData = [], $testEventCode = null)
    {
        $payload = [
            'data' => [
                [
                    'event_name' => $eventName,
                    'event_time' => time(),
                    'event_id' => $eventId,
                    'custom_data' => [
                        'currency' => 'BDT',
                        'value' => 2300,
                        'content_ids' => ["2"],
                        'content_type' => 'product',
                    ],
                    'user_data' => $userData,
                    'action_source' => 'website',
                ]
            ]
        ];

        if ($testEventCode) {
            $payload['test_event_code'] = $testEventCode;
        }
        dd($payload);
        $response = $this->client->post($this->pixelId . '/events', [
            'query' => [
                'access_token' => $this->accessToken,
            ],
            'json' => $payload,
        ]);


        return json_decode($response->getBody(), true);
    }

}
