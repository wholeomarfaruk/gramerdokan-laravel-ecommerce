<?php

namespace App\Services;

use Illuminate\Support\Str;

class DataLayerService
{
    public function base(array $data = []): array
    {
        return array_merge([
            'event_id' => (string) Str::uuid(),
            'page_type' => null,
            'user_data' => [
                'email' => null,
                'phone' => null,
                'fbp' => request()->cookie('_fbp'),
                'fbc' => request()->cookie('_fbc'),
            ],
            'ecommerce' => [],
        ], $data);
    }

    public function push(string $event, array $data = []): array
    {
        return array_merge([
            'event' => $event,
        ], $this->base($data));
    }

    public function render(string $event, array $data = []): string
    {
        $payload = $this->push($event, $data);

        return '<script>
window.dataLayer = window.dataLayer || [];
window.dataLayer.push(' . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');
</script>';
    }
}
