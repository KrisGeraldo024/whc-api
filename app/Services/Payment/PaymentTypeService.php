<?php

namespace App\Services\Payment;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\PaymentType;
use App\Traits\GlobalTrait; 


class PaymentTypeService
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
      $records = PaymentType::orderBy('created_at')
      ->with('paymentMethods')
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
        $record = PaymentType::create([
          'title'              => $request->title,
          'slug'                => $this->slugify($request->title, 'PaymentType'),
          'enabled'            => $request->enabled,
          'summary'            => $request->summary,
          'content'            => $request->content,
          'order'              => $request->order,
        ]);

        // $payment_method_ids = [];
        // if ($request->has('payment_methods')) {
        //     $payment_methods = json_decode($request->payment_methods);
        //     $payment_method_ids = array_map(function($item) {
        //         return $item->id;
        //     }, $payment_methods);
        // }

        // Log::info('File path received from request: ' . $request->payment_methods);
      // Attach payment methods to the payment type
      // $record->paymentMethods()->attach($request->payment_methods);

      // foreach ($request->payment_methods as $payment_method_id) {
      //   $record->paymentMethods()->attach($payment_method_id);
      // }

      $payment_methods = json_decode($request->payment_methods);
      foreach ($payment_methods as $payment_method_id) {
          $record->paymentMethods()->attach($payment_method_id);
      }

      $this->metatags($record, $request);
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
    public function show ($payment_type, $request): Response
    {
        $payment_type->load('metadata','paymentMethods');
        $this->generateLog($request->user(), "viewed this payment option ({$payment_type->id}).");
        return response([
            'record' => $payment_type
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($payment_type, $request): Response
    {
       $payment_type->update([
        'title'              => $request->title,
        'slug'               => $this->slugify($request->title, 'PaymentType', $payment_type->id),
        'enabled'            => $request->enabled,
        'summary'            => $request->summary,
        'content'            => $request->content,
        'order'              => $request->order,
        ]);

        $payment_methods = json_decode($request->payment_methods);
        foreach ($payment_methods as $payment_method_id) {
            $payment_type->paymentMethods()->sync($payment_method_id);
        }

        $this->metatags($payment_type, $request);

        $this->generateLog($request->user(), "updated this payment option ({$payment_type->id}).");
        return response([
            'record' => $payment_type
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($payment_type, $request): Response
    {
        $payment_type->paymentMethods()->detach();
        $payment_type->delete();
        $this->generateLog($request->user(), "deleted this payment option ({$payment_type->id}).");
        return response([
            'record' => 'Payment Option deleted'
        ]);
    }
}
