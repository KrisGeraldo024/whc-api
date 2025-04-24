<?php

namespace App\Services\taxonomies;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\TaxInformationsDetails;
use App\Traits\GlobalTrait;

class TaxInformationsDetailsServices
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * StatService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {

        $records = TaxInformationsDetails::orderBy('sequence')
        ->when($request->filled('parent_id'), function ($query) use ($request) {
            $query->whereParentId($request->parent_id);
        })

        ->when($request->filled('all') , function ($query) {
            return $query->get();
        }, function ($query) {
            return $query->paginate(20);
        });

        return response([
            'records' => $records
        ]);
    }

    /**
     * StatService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = TaxInformationsDetails::create($request->all());

        if ($request->has('main_image_informations_details')) {
          $this->addImages('tax_informations_details', $request, $record, 'main_image_informations_details');
        }
        if ($request->has('mobile_image_informations_details')) {
            $this->addImages('tax_informations_details', $request, $record, 'mobile_image_informations_details');
        }

        $this->generateLog($request->user(), "added this Informations Details ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * StatService show
     * @param  TaxInformationsDetails $taxInformationsDetail
     * @param  Request $request
     * @return Response
     */
    public function show ( $request, $taxInformationsDetail): Response
    {

        $taxInformationsDetail->load('images');

        $this->generateLog($request->user(), "viewed this Informations Details ({$taxInformationsDetail->id}).");
        
        return response([
            'record' => $taxInformationsDetail
        ]);
    }

    /**
     * StatService update
     * @param  TaxInformationsDetails $taxInformationsDetail
     * @param  Request $request
     * @return Response
     */
    public function update ($request,$taxInformationsDetail): Response
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $taxInformationsDetail->update([
            'title'    => $request->title,
            'description' => $request->description,
            'sequence'    => $request->sequence
        ]);

        $this->updateImages('tax_informations_details', $request, $taxInformationsDetail, 'main_image_informations_details');
        $this->updateImages('tax_informations_details', $request, $taxInformationsDetail, 'mobile_image_informations_details');

        $this->generateLog($request->user(), "updated this Informations Details ({$taxInformationsDetail->id}).");

        return response([
            'record' => $taxInformationsDetail
        ]);
    }

    /**
     * StatService destroy
     * @param  Informations Details $Informations Details
     * @param  Request $request
     * @return Response
     */
    public function destroy ($request,$taxInformationsDetail): Response
    {
        $taxInformationsDetail->delete();
        $this->generateLog($request->user(), "deleted this Informations Details ({$taxInformationsDetail->id}).");

        return response([
            'record' => 'Informations Details deleted'
        ]);
    }
}
