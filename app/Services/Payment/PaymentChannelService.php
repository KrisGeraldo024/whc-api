<?php

namespace App\Services\Payment;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\PaymentChannel;
use App\Traits\GlobalTrait; 

class PaymentChannelService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * FaqService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
      $records = PaymentChannel::orderBy('title')
      ->when($request->filled('all'), function ($query) {
          return $query->get();
      }, function ($query) {
          return $query->paginate(20);
      });

      return response([
          'records' => $records
      ]);
    }

    /**
     * FaqService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $record = PaymentChannel::create([
            'title'    => $request->title,
        ]);

        if ($request->hasFile('logo')) {
            $this->addImages('payment_channel', $request, $record, 'logo');
        }
        
        $this->generateLog($request->user(), "added this payment channel ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * FaqService show
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function show ($payment_channel, $request): Response
    {
        $payment_channel->load('images');
        $this->generateLog($request->user(), "viewed this payment channel ({$payment_channel->id}).");

        return response([
            'record' => $payment_channel
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($payment_channel, $request): Response
    {
       $payment_channel->update([
            'title'    => $request->title,
        ]);

        $this->updateImages('payment_channel', $request, $payment_channel, 'logo');
        $this->generateLog($request->user(), "updated this payment channel ({$payment_channel->id}).");

        return response([
            'record' => $payment_channel
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($payment_channel, $request): Response
    {
        $payment_channel->delete();
        $this->generateLog($request->user(), "deleted this payment channel ({$payment_channel->id}).");
        return response([
            'record' => 'Payment Channel deleted'
        ]);
    }
}
