<?php

namespace App\Services\Newsletter;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MailchimpService
{
    private string $apiKey;
    private string $listId;
    private string $dataCenter;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.mailchimp.api_key');
        $this->listId = config('services.mailchimp.list_id');
        $this->dataCenter = explode('-', $this->apiKey)[1];
        $this->baseUrl = "https://{$this->dataCenter}.api.mailchimp.com/3.0";
    }

    public function subscribe(string $email): array
    {
        // Log the subscription attempt
        Log::info('Mailchimp subscription attempt', ['email' => $email]);

        try {
            // First, check if the member exists
            $subscriberHash = md5(strtolower($email));
            $checkResponse = Http::withBasicAuth('apikey', $this->apiKey)
                ->get("{$this->baseUrl}/lists/{$this->listId}/members/{$subscriberHash}");

            // Log the response from checking the member status
            Log::info('Mailchimp member check response', [
                'email' => $email,
                'response' => $checkResponse->json()
            ]);

            if ($checkResponse->successful()) {
                $memberInfo = $checkResponse->json();

                // Check different member statuses
                switch ($memberInfo['status']) {
                    case 'subscribed':
                        return [
                            'success' => false,
                            'message' => 'This email is already subscribed to our newsletter.',
                            'status' => 'already_subscribed'
                        ];
                    case 'pending':
                        return [
                            'success' => false,
                            'message' => 'Please check your email for the confirmation link we previously sent.',
                            'status' => 'pending_confirmation'
                        ];
                    case 'unsubscribed':
                        return [
                            'success' => false,
                            'message' => 'This email was previously unsubscribed. Would you like to resubscribe?',
                            'status' => 'can_resubscribe'
                        ];
                }
            }

            // If member doesn't exist or other status, try to subscribe them
            $response = Http::withBasicAuth('apikey', $this->apiKey)
                ->post("{$this->baseUrl}/lists/{$this->listId}/members", [
                    'email_address' => $email,
                    'status' => 'pending',
                    'double_optin' => true
                ]);

            // Log the response of the subscription attempt
            Log::info('Mailchimp subscription response', [
                'email' => $email,
                'response' => $response->json()
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Please check your email to confirm your subscription.',
                    'status' => 'confirmation_sent'
                ];
            }

            // Handle specific API errors
            $error = $response->json();
            Log::error('Mailchimp API Error', [
                'email' => $email,
                'error' => $error
            ]);

            return [
                'success' => false,
                'message' => $this->getErrorMessage($error),
                'status' => 'error'
            ];
        } catch (Exception $e) {
            Log::error('Mailchimp Service Error', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while processing your request. Please try again later.',
                'status' => 'error'
            ];
        }
    }

    private function getErrorMessage($error): string
    {
        if (isset($error['title']) && $error['title'] === 'Invalid Resource') {
            return 'Please provide a valid email address.';
        }

        if (isset($error['detail'])) {
            return 'Unable to process subscription: ' . $error['detail'];
        }

        return 'Failed to process subscription request. Please try again later.';
    }
}
