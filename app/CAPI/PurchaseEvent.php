<?php

namespace App\CAPI;

use App\Helper\Convert;
use Illuminate\Support\Facades\Http;

class PurchaseEvent
{

      //event data
    protected ?string $event_name = 'Purchase';

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
    protected ?array $content_ids= [];
    protected ?string $content_name;
    protected ?string $content_type;
    protected ?string $content_category;

    protected ?array $contents = [];
    protected ?array $ecommerce = [];
    protected ?float $shipping_cost;
    protected ?string $delivery_category='home_delivery';
    protected ?string $order_id;

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
        $this->customer_id = data_get($user,'id',null);


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
        ?string $currency = null,
        ?float $contentPrice = null,
        ?array $contentIds = [],
        ?string $content_type = null,
        ?array $contents = [],
        ?array $ecommerce = [],
        ?float $shipping_cost = null,
        ?string $order_id = null
    ) {

       $this->event_id = $eventId ?? $this->event_id;
        $this->currency = $currency ?? $this->currency;
        $this->content_price = $contentPrice ?? $this->content_price;
        $this->content_ids = $contentIds ?? $this->content_ids;
        $this->content_type = $content_type ?? $this->content_type;
        $this->contents = $contents;
        $this->ecommerce = $ecommerce;
        $this->shipping_cost = $shipping_cost;
        $this->order_id = $order_id ?? $this->order_id;
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

        return [
            'event_name' => $this->event_name,
            'event_time' => $this->event_time,
            'event_id' => $this->event_id,
            'action_source' => $this->action_source,
            'event_source_url' => $this->event_source_url,

            'custom_data' => [
                'currency' => $this->currency,
                'order_id' => $this->order_id,
                'content_ids' => $this->content_ids,
                'value' => $this->content_price,
                'content_type' => $this->content_type,
                'contents' => $this->contents,
                'shipping_cost' => $this->shipping_cost,
                'num_items' => is_array($this->contents) ? count($this->contents) : null,
                'delivery_category' => $this->delivery_category,
            ],

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
    }

    public function render(string $event, array $data = []): string
    {
        $payload = $this->payload();
        $payload['event']='purchase';
        return '<script>
window.dataLayer = window.dataLayer || [];
window.dataLayer.push(' . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');
</script>';
    }

    public function sendServerSide(): array
    {


        $payload = [
            'data' => [
                $this->payload(),
            ],
        ];

       if (session('debugmode')) {
            $payload['test_event_code'] = $this->test_event_code;
        }

        $url = "https://graph.facebook.com/v23.0/{$this->pixel_id}/events";

        $response = Http::post($url, array_merge($payload, [
            'access_token' => $this->access_token,
        ]));


        return [
            'ok' => $response->successful(),
            'status' => $response->status(),
            'response' => $response->json(),
        ];
    }
       public function browserEventPayload(): array
    {
        $payload = $this->payload();
        $payload['event']='purchase';
        $payload['ecommerce'] = $this->ecommerce;


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
}
