<?php

namespace App\Services\Payment;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\PaymentOption;
use App\Traits\GlobalTrait; 

class PaymentOptionService
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
      $records = PaymentOption::orderBy('created_at')
      ->with(['payment_methods'])
      ->with(['payment_channels'])
      ->with(['payment_platforms'])
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
        $record = PaymentOption::create([
          'payment_method_id'               => $request->payment_method_id,
          'payment_platform_id'             => $request->payment_platform_id,
          'payment_channel_id'              => $request->payment_channel_id,
          'enabled'                         => $request->enabled,
          'is_other_services'               => $request->is_other_services,
        ]);

        if ($request->hasFile('steps')) {
            $this->addImages('payment_option', $request, $record, 'steps');
        }
        $this->generateLog($request->user(), "added this payment option ({$record->id}).");
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
    public function show ($payment_option, $request): Response
    {
        $payment_option->load('images');
        $this->generateLog($request->user(), "viewed this payment option ({$payment_option->id}).");
        return response([
            'record' => $payment_option
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($payment_option, $request): Response
    {
       $payment_option->update([
        'payment_method_id'               => $request->payment_method_id,
        'payment_platform_id'             => $request->payment_platform_id,
        'payment_channel_id'              => $request->payment_channel_id,
        'enabled'                         => $request->enabled,
        'is_other_services'               => $request->is_other_services,
        ]);

        $this->updateImages('payment_option', $request, $payment_option, 'steps');
        $this->generateLog($request->user(), "updated this payment option ({$payment_option->id}).");
        return response([
            'record' => $payment_option
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($payment_option, $request): Response
    {
        $payment_option->delete();
        $this->generateLog($request->user(), "deleted this payment option ({$payment_option->id}).");
        return response([
            'record' => 'Payment Option deleted'
        ]);
    }
}
