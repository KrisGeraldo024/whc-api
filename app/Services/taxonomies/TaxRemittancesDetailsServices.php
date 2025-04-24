<?php

namespace App\Services\taxonomies;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\TaxRemittancesDetails;
use App\Traits\GlobalTrait;

class TaxRemittancesDetailsServices
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

        $records = TaxRemittancesDetails::orderBy('sequence')
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

        $record = TaxRemittancesDetails::create($request->all());

        if ($request->has('main_image_remittances_details')) {
          $this->addImages('tax_remittances_details', $request, $record, 'main_image_remittances_details');
        }
        if ($request->has('mobile_image_remittances_details')) {
            $this->addImages('tax_remittances_details', $request, $record, 'mobile_image_remittances_details');
        }

        $this->generateLog($request->user(), "added this Remittances Details ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * StatService show
     * @param  TaxRemittancesDetail $taxRemittancesDetail
     * @param  Request $request
     * @return Response
     */
    public function show ( $request, $taxRemittancesDetail): Response
    {

        $taxRemittancesDetail->load('images');

        $this->generateLog($request->user(), "viewed this Remittances Details ({$taxRemittancesDetail->id}).");
        
        return response([
            'record' => $taxRemittancesDetail
        ]);
    }

    /**
     * StatService update
     * @param  TaxRemittancesDetail $taxRemittancesDetail
     * @param  Request $request
     * @return Response
     */
    public function update ($request,$taxRemittancesDetail): Response
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $taxRemittancesDetail->update([
            'title'    => $request->title,
            'description' => $request->description,
            'sequence'    => $request->sequence
        ]);

        $this->updateImages('tax_remittances_details', $request, $taxRemittancesDetail, 'main_image_remittances_details');
        $this->updateImages('tax_remittances_details', $request, $taxRemittancesDetail, 'mobile_image_remittances_details');

        $this->generateLog($request->user(), "updated this Remittances Details ({$taxRemittancesDetail->id}).");

        return response([
            'record' => $taxRemittancesDetail
        ]);
    }

    /**
     * StatService destroy
     * @param  Remittances Details $Remittances Details
     * @param  Request $request
     * @return Response
     */
    public function destroy ($request,$taxRemittancesDetail): Response
    {
        $taxRemittancesDetail->delete();
        $this->generateLog($request->user(), "deleted this Remittances Details ({$taxRemittancesDetail->id}).");

        return response([
            'record' => 'Remittances Details deleted'
        ]);
    }
}
