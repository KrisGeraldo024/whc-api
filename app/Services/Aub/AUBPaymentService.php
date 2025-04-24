<?php

namespace App\Services\Aub;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class AUBPaymentService
{
    private string $merchantId;
    private string $merchantKey;
    private string $gatewayUrl;

    private array $paymentServices = [
        'gcash' => 'pay.gcash.webpay',
        'qrph' => 'pay.instapay.native.v2',
        'grabpay' => 'pay.grab.webpay',
        'unionpay' => 'pay.upi.native.intl',
        'wechatpay' => 'pay.weixin.native.intl'
    ];

    public function __construct()
    {
        $this->merchantId = config('payment.aub.merchant_id');
        $this->merchantKey = config('payment.aub.merchant_key');
        $this->gatewayUrl = config('payment.aub.gateway_url');
    }

    public function createPayment(array $paymentData): array
    {
        Log::info('Payment Request', $paymentData);

        if (!isset($paymentData['paymentType'])) {
            throw new \InvalidArgumentException('Payment type is required');
        }

        if (!isset($this->paymentServices[$paymentData['paymentType']])) {
            throw new \InvalidArgumentException('Invalid payment type: ' . $paymentData['paymentType']);
        }

        // Store the selected payment service type
        $selectedService = $paymentData['paymentType'];
        $serviceCode = $this->paymentServices[$selectedService];

        $params = [
            'service' => $serviceCode,
            'mch_id' => $this->merchantId,
            'out_trade_no' => $paymentData['transaction_id'],
            'body' => $paymentData['description'] ?? 'Payment',
            'total_fee' => (string)$paymentData['amount'],
            'mch_create_ip' => request()->ip(),
            'notify_url' => config('app.url') . '/api/v1/web/payment/notify',
            'callback_url' => config('app.url') . '/api/v1/web/payment/callback',
            'nonce_str' => $this->generateNonceStr(),
            'sign_type' => 'SHA256',
            'version' => '2.0',
            'device_info' => '100'
        ];

        ksort($params);

        $stringToHash = $this->createStringToHash($params);
        $signature = strtoupper(hash('sha256', $stringToHash));
        $params['sign'] = $signature;

        $xml = $this->createXMLRequest($params);

        // Send request to gateway and add the payment service type to the response
        $response = $this->sendRequest($xml);
        $response['paymentService'] = $selectedService; // Add payment service type

        return $response;
    }

    private function generateNonceStr(int $length = 32): string
    {
        return Str::random($length);
    }

    private function createStringToHash(array $params): string
    {
        $stringParts = [];
        foreach ($params as $key => $value) {
            if ($value !== '' && $value !== null) {
                $stringParts[] = $key . '=' . $value;
            }
        }
        return implode('&', $stringParts) . '&key=' . $this->merchantKey;
    }

    private function createXMLRequest(array $params): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><xml></xml>');

        foreach ($params as $key => $value) {
            if ($value !== '' && $value !== null) {
                $xml->addChild($key, $value);
            }
        }

        return $xml->asXML();
    }

    private function sendRequest(string $xml): array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'text/xml; charset=UTF-8'
        ])->withBody($xml, 'text/xml')
            ->post($this->gatewayUrl);

        if ($response->successful()) {
            return $this->parseResponse($response->body());
        }

        throw new \Exception('Payment gateway request failed: ' . $response->body());
    }

    private function parseResponse(string $xmlResponse): array
    {
        $xml = simplexml_load_string($xmlResponse);
        if ($xml === false) {
            throw new \Exception('Failed to parse XML response');
        }

        return [
            'status' => (string)$xml->status,
            'message' => (string)$xml->message,
            'version' => (string)$xml->version,
            'charset' => (string)$xml->charset,
            'result_code' => (string)($xml->result_code ?? ''),
            'pay_url' => (string)($xml->pay_url ?? '')
        ];
    }
}
