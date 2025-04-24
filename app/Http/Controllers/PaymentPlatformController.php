<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentPlatformRequest;
use App\Models\{PaymentMethod, PaymentPlatform};
use Illuminate\Http\{Request, Response};
use App\Services\Payment\PaymentPlatformService;

class PaymentPlatformController extends Controller
{
    protected $paymentPlatformService;

    public function __construct(PaymentPlatformService $paymentPlatformService)
    {
        $this->paymentPlatformService = $paymentPlatformService;
    }

    public function index(Request $request): Response
    {
        return $this->paymentPlatformService->index($request);
    }

    public function store(PaymentPlatformRequest $request): Response
    {
        return $this->paymentPlatformService->store($request);
    }

    public function show(PaymentMethod $payment_method, PaymentPlatform $payment_platform, Request $request): Response
    {
        return $this->paymentPlatformService->show($payment_platform, $request);
    }

    public function update(PaymentMethod $payment_method, PaymentPlatform $payment_platform,PaymentPlatformRequest $request): Response
    {
        return $this->paymentPlatformService->update($payment_platform, $request);
    }

   public function destroy(PaymentMethod $payment_method, PaymentPlatform $payment_platform, Request $request): Response
    {
        return $this->paymentPlatformService->destroy($payment_platform, $request);
    }

    public function byPaymentMethod(PaymentMethod $payment_method, Request $request): Response
    {
        return $this->paymentPlatformService->getByPaymentMethod($payment_method, $request);
    }

    public function getPaymentPlatform(Request $request): Response 
    {
        return $this->paymentPlatformService->getPaymentPlatform($request);
    }
}