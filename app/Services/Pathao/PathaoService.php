<?php

namespace App\Services\Pathao;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PathaoService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl = config('services.pathao.base_url');

        $this->clientId = config('services.pathao.client_id');
        $this->clientSecret = config('services.pathao.client_secret');
        $this->username = config('services.pathao.username');
        $this->password = config('services.pathao.password');
    }

    /**
     * Get Access Token (cached)
     */
    public function getToken(): string
    {
        return Cache::remember('pathao_token', 3500, function () {

            $response = Http::post($this->baseUrl . '/aladdin/api/v1/issue-token', [
                'client_id'     => $this->clientId,
                'client_secret'  => $this->clientSecret,
                'username'      => $this->username,
                'password'      => $this->password,
                'grant_type'    => 'password',
            ]);

            if (!$response->successful()) {
                throw new \Exception('Pathao Auth Failed: ' . $response->body());
            }

            return $response->json()['access_token'];
        });
    }

    /**
     * Base Request
     */
    public function request(string $method, string $endpoint, array $data = [])
    {
        $token = $this->getToken();

        $response = Http::withToken($token)
            ->$method($this->baseUrl . $endpoint, $data);

        if ($response->status() === 401) {
            Cache::forget('pathao_token');
            return $this->request($method, $endpoint, $data);
        }

        if (!$response->successful()) {
            throw new \Exception($response->body());
        }

        return $response->json();
    }

    /**
     * Cities
     */
    public function cities()
    {
        return $this->request('get', '/aladdin/api/v1/city-list');
    }

    /**
     * Zones
     */
    public function zones($cityId)
    {
        return $this->request('get', "/aladdin/api/v1/cities/{$cityId}/zone-list");
    }

    /**
     * Areas
     */
    public function areas($zoneId)
    {
        return $this->request('get', "/aladdin/api/v1/zones/{$zoneId}/area-list");
    }

    /**
     * Create Order
     */
    public function createOrder(array $payload)
    {
        return $this->request('post', '/aladdin/api/v1/orders', $payload);
    }
}