<?php

namespace App\Http\Controllers;

use App\Models\PaymentChannel;
use Illuminate\Http\{
    Request,
    Response
};
use App\Http\Requests\PaymentChannelRequest;
use App\Services\Payment\PaymentChannelService;


class PaymentChannelController extends Controller
{
    protected $paymentChannelService;

    public function __construct (PaymentChannelService $paymentChannelService)
    {
        $this->paymentChannelService = $paymentChannelService;
    }

    public function index (Request $request): Response
    {
        return $this->paymentChannelService->index($request);
    }

    public function store (PaymentChannelRequest $request): Response
    {
        return $this->paymentChannelService->store($request);
    }
  
    public function show (PaymentChannel $payment_channel, Request $request): Response
    {
        return $this->paymentChannelService->show($payment_channel, $request);
    }
    
    public function update (PaymentChannel $payment_channel, PaymentChannelRequest $request): Response
    {
        return $this->paymentChannelService->update($payment_channel, $request);
    }
    
    public function destroy (PaymentChannel $payment_channel, Request $request): Response
    {
        return $this->paymentChannelService->destroy($payment_channel, $request);
    }
}
