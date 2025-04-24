<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Services\Aub\AUBPaymentService;
use Illuminate\Http\Request;

class AUBPaymentController extends Controller
{
    private $aubPaymentService;

    public function __construct(AUBPaymentService $aubPaymentService)
    {
        $this->aubPaymentService = $aubPaymentService;
    }

    public function initiatePayment(Request $request)
    {
        // Validate the request
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'paymentType' => 'required|string|in:gcash,qrph,grabpay,unionpay,wechatpay'
        ]);

        $paymentData = [
            'transaction_id' => uniqid('TRX'), // Generate unique transaction ID
            'amount' => $request->amount,
            'description' => $request->description,
            'paymentType' => $request->paymentType
        ];

        try {
            $response = $this->aubPaymentService->createPayment($paymentData);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        Log::info('Payment Callback Received', $request->all());

        // Implement your callback logic here
        // This is where AUB will send the payment result

        return response()->json(['status' => 'success']);
    }

    public function notify(Request $request)
    {
        Log::info('Payment Notification Received', $request->all());

        // Implement your notification logic here
        // This is for asynchronous updates from AUB

        return response()->json(['status' => 'success']);
    }
}
