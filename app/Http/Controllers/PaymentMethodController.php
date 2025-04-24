<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\{
    Request,
    Response
};
use App\Http\Requests\PaymentMethodRequest;
use App\Services\Payment\PaymentMethodService;

class PaymentMethodController extends Controller
{
    protected $paymentMethodService;

    public function __construct (PaymentMethodService $paymentMethodService)
    {
        $this->paymentMethodService = $paymentMethodService;
    }

    public function index (Request $request): Response
    {
        return $this->paymentMethodService->index($request);
    }

    public function store (Request $request): Response
    {
        return $this->paymentMethodService->store($request);
    }
  
    public function show (PaymentMethod $payment_method, Request $request): Response
    {
        return $this->paymentMethodService->show($payment_method, $request);
    }
    
    public function update (PaymentMethod $payment_method, Request $request): Response
    {
        return $this->paymentMethodService->update($payment_method, $request);
    }
    
    public function destroy (PaymentMethod $payment_method, Request $request): Response
    {
        return $this->paymentMethodService->destroy($payment_method, $request);
    }

    public function getMethods (Request $request): Response
    {
        return $this->paymentMethodService->getMethods($request);
    }
}
