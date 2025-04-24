<?php

namespace App\Services\Bux;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BuxPaymentService
{
    protected $apiKey;
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl = 'https://api.bux.ph/v1/api';

    public function __construct()
    {
        $this->apiKey = config('services.bux.api_key');
        $this->clientId = config('services.bux.client_id');
        $this->clientSecret = config('services.bux.client_secret');
    }

    /**
     * Generate a checkout link
     */
    public function generateCheckout(array $data)
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->post($this->baseUrl . '/open/checkout/', array_merge([
                'client_id' => $this->clientId,
            ], $data));

            return $response->json();
        } catch (\Exception $e) {
            Log::error('BUX Checkout Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate a direct payment link
     */
    public function generateDirect(array $data)
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->post($this->baseUrl . '/generate_code/', array_merge([
                'client_id' => $this->clientId,
            ], $data));

            return $response->json();
        } catch (\Exception $e) {
            Log::error('BUX Direct Payment Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check payment status
     */
    public function checkStatus(string $reqId)
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->get($this->baseUrl . '/check_code/', [
                'req_id' => $reqId,
                'client_id' => $this->clientId,
                'mode' => 'API'
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('BUX Status Check Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch available payment channels
     */
    public function getChannels()
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->get($this->baseUrl . '/channel_codes/', [
                'client_id' => $this->clientId
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('BUX Channels Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify postback signature
     */
    public function verifyPostbackSignature(string $reqId, string $status, string $signature)
    {
        $calculated = sha1($reqId . $status . '{' . $this->clientSecret . '}');
        return hash_equals($calculated, $signature);
    }
}
