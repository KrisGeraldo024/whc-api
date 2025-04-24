<?php

namespace App\Services\Payment;
use Illuminate\Support\Str;
use App\Models\Accordion;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator,
};
use App\Models\PaymentPlatform;
use App\Traits\GlobalTrait; 

class PaymentPlatformService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
      $records = PaymentPlatform::orderBy('title')
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
     * FaqService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $sequence = $request->sequence;
        if (empty($sequence)) {
            $sequence = PaymentPlatform::whereNull('deleted_at')
                ->where('payment_method_id', $request->payment_method_id)
                ->max('sequence') + 1;
        }
        $record = PaymentPlatform::create([
            'payment_method_id' => $request->payment_method_id,
            'title'             => $request->title,
            'identifier'        => $this->slugify($request->title, 'PaymentPlatform'),
            'sequence'          => $sequence,
            'buttonText'        => $request->button_name ?? '',
            'buttonLink'        => $request->link ?? '',
            'buttonIsLinkOut'   => $request->is_link_out,
            'buttonActive'      => $request->isPublished,
        ]);

        if ($request->has('accordion_title')) {
            $this->updateOrCreateAccordions($request, $record);
        }

        if($request->has('main_image')){
            $this->addImages('payment_platform', $request, $record, 'main_image');
        }

        // if ($request->hasFile('logo')) {
        //     $this->addImages('payment_platform', $request, $record, 'logo');
        // }
        $record->load([
            'logs' => fn($q) => $q->orderBy('updated_at', 'desc')
                ->with(['user.images', 'user.userDetail'])
        ]);
        
        $this->generateLog($request->user(), "Created", "Payment Channels", $record);

        return response([
            'record' => $record
        ]);
    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function show ($payment_platform, $request): Response
    {

        $payment_platform->load('images');

        $payment_platform->load([
            'logs' => fn($q) => $q->orderBy('updated_at', 'desc')
                ->with(['user.images', 'user.userDetail']),
        ]);

        $payment_platform->load(['accordions' => function ($q) {
            $q->orderBy('order');
        }]);
        
        // $this->generateLog($request->user(), "viewed this payment platform ({$payment_platform->id}).");

        return response([
            'record' => $payment_platform
        ]);
    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function update ($payment_platform, $request): Response
    {
       $payment_platform->update([
            'title'             => $request->title,
            'identifier'        => $request->title !== $payment_platform->title ? $this->slugify($request->title, 'PaymentPlatform', $payment_platform->id) : $payment_platform->identifier,
            'sequence'          => $payment_platform->sequence,
            'buttonText'        => $request->button_name ?? '',
            'buttonLink'        => $request->link ?? '',
            'buttonIsLinkOut'   => $request->is_link_out,
            'buttonActive'      => $request->isPublished,
        ]);

        if ($request->has('accordion_title')) {
            $this->updateOrCreateAccordions($request, $payment_platform);
        }
        if($request->has('main_image_id')){
            $this->updateImages('payment_platform', $request, $payment_platform, 'main_image');
        }

        $payment_platform->load([
            'logs' => fn($q) => $q->orderBy('updated_at', 'desc')
                ->with(['user.images', 'user.userDetail'])
        ]);
        $this->generateLog($request->user(), "Changed", "Payment Channels", $payment_platform);

        return response([
            'record' => $payment_platform
        ]);
    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function destroy ($payment_platform, $request): Response
    {
        $this->generateLog($request->user(), "Deleted", "Payment Channels", $payment_platform);
        $payment_platform->delete();
        $this->reassignOrderValues('PaymentPlatform',  $payment_platform->payment_method_id);
        return response([
            'record' => 'Payment Platform deleted'
        ]);
    }

        /**
     * Get payment platforms by payment method
     * @param  PaymentMethod $payment_method
     * @param  Request $request
     * @return Response
     */
    public function getByPaymentMethod($payment_method, $request): Response
    {
        $records = $payment_method->paymentPlatforms()
            ->orderBy('title')
            ->with('images')
            ->when($request->filled('all'), function ($query) {
                return $query->get();
            }, function ($query) {
                return $query->paginate(10);
            });

        return response([
            'records' => $records
        ]);
    }


    public function getPaymentPlatform($request) : Response
    {
        $record = PaymentPlatform::where('identifier', $request->identifier)
        ->with(['accordions' => function ($q) {
            $q->orderBy('order');
        }])
        ->with('images') // Added here
        ->first();
    

        return response([
            'record' => $record 
        ]);
    }

    
    protected function updateOrCreateAccordions($request, $page_section)
    {
        foreach ($request->accordion_title as $key => $title) {
            $accordion = Accordion::find($request->accordion_id[$key]) ?? new Accordion(['parent' => $page_section->id]);
            $accordion->fill([
                'title' => $title,
                'description' => $request->accordion_description[$key],
                'order' => $request->accordion_order[$key],
            ])->save();
        }
    }

    /**
     * Generate a unique slug
     * @param  string $title
     * @param  string $modelClass
     * @return string
     */
    private function slugify(string $title, $ref_id = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        // $counter = 1;

        $record =  PaymentPlatform::whereIdentifier( $slug )->first();
        if ( !is_null( $record ) ) {
            // $query  = $MODEL::where('slug','like', $slug.'%')->whereNull('deleted_at');
            $query  = PaymentPlatform::where('identifier', 'LIKE', $slug.'%')->withTrashed();

            if (!is_null($ref_id)){
                $query = $query->where('id','!=',$ref_id);
            }

            $count  = $query->latest('id')->count();
            if ($count > 0) {
                $count++;
                $slug = "{$slug}-{$count}";
            }
        }
        // while (PaymentPlatform::where('identifier', $slug)->exists()) {
        //     $slug = "{$baseSlug}-{$counter}";
        //     $counter++;
        // }

        return $slug;
    }
    
    public function reassignOrderValues(string $model, string $payment_method_id = null)
    {
        $modelClass = "App\\Models\\$model";
        $order = 1;

        $modelClass::orderBy('sequence') // Ensure ordered processing
            ->when(isset($payment_method_id), function ($query) use ($payment_method_id) {
                $query->where('payment_method_id', $payment_method_id);
            })
            ->chunkById(100, function ($items) use (&$order) {
                foreach ($items as $item) {
                    $item->update(['sequence' => $order++]);
                }
            });
    }
}
