<?php

namespace App\Http\Controllers;

use App\Models\PaymentOption;
use Illuminate\Http\{
    Request,
    Response
};
use App\Http\Requests\PaymentOptionRequest;
use App\Services\Payment\PaymentOptionService;

class PaymentOptionController extends Controller
{
    protected $paymentOptionService;

    public function __construct (PaymentOptionService $paymentOptionService)
    {
        $this->paymentOptionService = $paymentOptionService;
    }

    public function index (Request $request): Response
    {
        return $this->paymentOptionService->index($request);
    }

    public function store (PaymentOptionRequest $request): Response
    {
        return $this->paymentOptionService->store($request);
    }
  
    public function show (PaymentOption $payment_option, Request $request): Response
    {
        return $this->paymentOptionService->show($payment_option, $request);
    }
    
    public function update (PaymentOption $payment_option, PaymentOptionRequest $request): Response
    {
        return $this->paymentOptionService->update($payment_option, $request);
    }
    
    public function destroy (PaymentOption $payment_option, Request $request): Response
    {
        return $this->paymentOptionService->destroy($payment_option, $request);
    }
}
