<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use Illuminate\Http\{
    Request,
    Response
};
use App\Http\Requests\PaymentTypeRequest;
use App\Services\Payment\PaymentTypeService;

class PaymentTypeController extends Controller
{
    protected $paymentTypeService;

    public function __construct (PaymentTypeService $paymentTypeService)
    {
        $this->paymentTypeService = $paymentTypeService;
    }

    public function index (Request $request): Response
    {
        return $this->paymentTypeService->index($request);
    }

    public function store (PaymentTypeRequest $request): Response
    {
        return $this->paymentTypeService->store($request);
    }
  
    public function show (PaymentType $payment_type, Request $request): Response
    {
        return $this->paymentTypeService->show($payment_type, $request);
    }
    
    public function update (PaymentType $payment_type, PaymentTypeRequest $request): Response
    {
        return $this->paymentTypeService->update($payment_type, $request);
    }
    
    public function destroy (PaymentType $payment_type, Request $request): Response
    {
        return $this->paymentTypeService->destroy($payment_type, $request);
    }
}
