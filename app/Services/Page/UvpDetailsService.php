<?php

namespace App\Services\Page;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\UvpDetails;
use App\Traits\GlobalTrait;

class UvpDetailsService
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

        $records = UvpDetails::orderBy('sequence')
        ->when($request->filled('uvp_id'), function ($query) use ($request) {
            $query->whereUvpId($request->uvp_id);
        })
      
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
       
        $record = UvpDetails::create($request->all());

        $this->generateLog($request->user(), "added this construction update ({$record->id}).");
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
    public function show ( $request, $uvpDetail): Response
    {
        $uvpDetail->load('images');
        $this->generateLog($request->user(), "viewed this construction update ({$uvpDetail->id}).");
        return response([
            'record' => $uvpDetail
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($request, $uvpDetail): Response
    {
        $uvpDetail->update([
           'uvp_id' => $request->uvp_id,
            'title' => $request->title,
            'description' => $request->description,
            'sequence' => $request->sequence,
            'uvp_type_details' => $request->uvp_type_details,
        ]);

        $this->generateLog($request->user(), "updated this construction update ({$uvpDetail->id}).");
        return response([
            'record' => $uvpDetail
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ( $request,$uvpDetail): Response
    {
        $uvpDetail->delete();
        $this->generateLog($request->user(), "deleted this construction update ({$uvpDetail->id}).");
        return response([
            'record' => 'Uvp details deleted'
        ]);
    }
}
