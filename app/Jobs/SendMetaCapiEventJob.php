<?php

namespace App\Jobs;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendMetaCapiEventJob implements ShouldQueue
{
    use Queueable;
    public int $tries = 3;
    public int $backoff = 10;
    public array $payload;
    private string $pixel_id;
    private string $access_token;
    private string $test_event_code;
    public $debug=false;
    /**
     * Create a new job instance.
     */
    public function __construct(array $payload,bool $debug=false)
    {
        $this->payload = $payload;
        $this->pixel_id = config('conversionapi.meta_pixel_id');
        $this->access_token = config('conversionapi.meta_access_token');
        $this->test_event_code = config('conversionapi.meta_test_code');
        $this->debug=$debug;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payload = $this->payload;
        $url = "https://graph.facebook.com/v23.0/{$this->pixel_id}/events";

        $response = Http::timeout(5)->post($url, array_merge($payload, [
            'access_token' => $this->access_token,
        ]));
        // Log::info(json_encode($payload,JSON_PRETTY_PRINT));
        // Log::info('Queue Meta CAPI Response:', $response->json());
        // $debug_text=$this->debug==true? 'ON':'OFF';
        // Log::info('debugmode: '.$debug_text);
    }
}
