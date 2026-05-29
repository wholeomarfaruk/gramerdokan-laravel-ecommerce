<?php

namespace App\CAPI;

use App\Helper\Convert;

class PageViewEvent
{

    //event data
    protected ?string $event_name = 'PageView';

    protected ?string $event_id;
    protected ?int $event_time;
    protected ?string $action_source;
    protected ?string $event_source_url;
    protected ?string $referrer_url;
    protected ?string $test_event_code;
    protected ?string $pixel_id;
    protected ?string $access_token;

    //ecommerce data
    protected ?string $currency = 'BDT';
    protected ?float $content_price;
    protected ?string $content_id;
    protected ?string $content_name;
    protected ?string $content_type;
    protected ?string $content_category;
    protected ?array $contents;
    //user data
    protected ?string $external_id;
    protected ?string $client_ip_address;
    protected ?string $client_user_agent;
    protected ?string $fbp;
    protected ?string $fbc;
    protected ?array $master_dl = [];
    protected ?string $first_name;
    protected ?string $last_name;
    protected ?string $email;
    protected ?string $phone;
    protected ?string $address; //address
    protected ?string $country;
    protected ?string $city;
    protected ?string $state;
    protected ?string $zipcode;
    protected ?string $street;
    protected ?string $customer_id;



    public function __construct()
    {
        $this->master_dl = json_decode(request()->cookie('master_dl'), true) ?? [];
        $this->event_time = time();
        $this->test_event_code = config('conversionapi.meta_test_code');
        $this->pixel_id = config('conversionapi.meta_pixel_id');
        $this->access_token = config('conversionapi.meta_access_token');
        $this->action_source = 'website';
        $this->event_source_url = url()->current();
        $this->client_ip_address = request()->ip();
        $this->client_user_agent = request()->userAgent();
        $this->external_id = request()->cookie('_sfdid');
        $this->fbp = data_get($this->master_dl, 'fbp') ?? request()->cookie('_fbp');
        $this->fbc = data_get($this->master_dl, 'fbc') ?? request()->cookie('_fbc');
        $this->referrer_url = data_get($this->master_dl, 'referrer_url');
        $this->event_id = time() . '_' . uniqid();
        //user data
        $user = data_get($this->master_dl, 'user', []);
        $this->customer_id = data_get($user, 'id', null);

        $map = [
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'email' => 'email',
            'phone' => 'phone',
            'address' => 'address',
            'country' => 'country',
            'city' => 'city',
            'state' => 'state',
            'zipcode' => 'zip_code',
            'street' => 'street',
        ];

        foreach ($map as $property => $key) {
            $value = data_get($user, $key);
            $this->$property = $value ? Convert::normalizeAndHash($value) : null;
        }
    }
    public function push(
        ?string $eventId = null,
    ) {
        $this->event_id = $eventId ?? $this->event_id;
    }
    public function get($variable)
    {

        return $this->$variable ?? null;
    }
    public function set($variable, $value)
    {

        if (!isset($this->$variable)) {
            return null;
        }
        $this->$variable = $value;

        return $this->get($variable);
    }

    public function payload(): array
    {

        $payload = [
            'event_name' => $this->event_name,
            'event_time' => $this->event_time,
            'event_id' => $this->event_id,
            'action_source' => $this->action_source,
            'event_source_url' => $this->event_source_url,
            'referrer_url' => $this->referrer_url,
            'user_data' => [
                'client_ip_address' => $this->client_ip_address,
                'client_user_agent' => $this->client_user_agent,
                'external_id' => $this->customer_id ?? $this->external_id,
                'fbp' => $this->fbp,
                'fbc' => $this->fbc,
                'fn' => $this->first_name,
                'ln' => $this->last_name,
                'em' => $this->email,
                'ph' => $this->phone,
                'country' => $this->country,
                'ct' => $this->city,
                'st' => $this->state,
                'zp' => $this->zipcode,
            ],

        ];

        return $payload;
    }
    public function serverPayload()
    {

        $payload = [
            'data' => [
                $this->payload(),
            ],
        ];

        if (session('debugmode')) {
            $payload['test_event_code'] = $this->test_event_code;
        }

        return $payload;
    }

    public function browserEventPayload(): array
    {
        $payload = $this->payload();
        $payload['event'] = $this->event_name;

        return $payload;
    }
    public function render(array $data = []): string
    {
        $payload = $this->payload();

        return '<script>
window.dataLayer = window.dataLayer || [];
window.dataLayer.push(' . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');
</script>';
    }

}
