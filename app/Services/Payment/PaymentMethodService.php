<?php

namespace App\Services\Payment;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\PaymentMethod;
use App\Traits\GlobalTrait; 

class PaymentMethodService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * Payment Method service index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
      $records = PaymentMethod::orderBy('title')
      ->when($request->filled('all'), function ($query) {
          return $query->get();
      }, function ($query) {
          return $query->paginate(10);
      });


      return response([
          'records' => $records
      ]);
    }


    public function getMethods ($request): Response
    {
        $records = PaymentMethod::with('images')  // Added here
            ->orderBy('title')
            ->when($request->filled('all'), function ($query) {
                return $query->get();
            }, function ($query) {
                return $query->paginate(10);
            });
    
        return response([
            'records' => $records
        ]);
    }

    /**
     * Payment Method service store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $record = PaymentMethod::create([
          'title'    => $request->title,
          'published'    => $request->published,
        ]);

        if($request->has('main_image')){
            $this->addImages('payment_method', $request, $record, 'main_image');
        }

        $this->generateLog($request->user(), "Created", "Payment Channels", $record);

        return response([
            'record' => $record
        ]);
    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function show ($payment_method, $request): Response
    {
        $payment_method->load('images');
        // $this->generateLog($request->user(), "viewed this award ({$payment_method->id}).");
        return response([
            'record' => $payment_method
        ]);
    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function update ($payment_method, $request): Response
    {
        $payment_method->update([
          'title'    => $request->title,
          'published'    => $request->published,
        ]);

        if($request->has('main_image')) {
            $this->updateImages('payment_method', $request, $payment_method, 'main_image');
        }

        $this->generateLog($request->user(), "Changed", "Payment Channels", $payment_method);

        return response([
            'record' => $payment_method
        ]);
    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function destroy ($payment_method, $request): Response
    {
        $this->generateLog($request->user(), "Deleted", "Payment Channels", $payment_method);
        $payment_method->delete();
        return response([
            'record' => 'Payment Method deleted'
        ]);
    }
}
