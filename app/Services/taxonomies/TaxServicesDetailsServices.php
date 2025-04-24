<?php

namespace App\Services\taxonomies;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\taxServicesDetails;
use App\Traits\GlobalTrait;

class TaxServicesDetailsServices
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

        $records = taxServicesDetails::orderBy('sequence')
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

        $record = taxServicesDetails::create($request->all());

        $this->generateLog($request->user(), "added this stat ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * StatService show
     * @param  taxServices $taxservices
     * @param  Request $request
     * @return Response
     */
    public function show ( $request,$taxServicesDetail): Response
    {
        $this->generateLog($request->user(), "viewed this stat ({$taxServicesDetail->id}).");
        
        return response([
            'record' => $taxServicesDetail
        ]);
    }

    /**
     * StatService update
     * @param  taxServices $taxservices
     * @param  Request $request
     * @return Response
     */
    public function update ($request,$taxServicesDetail): Response
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $taxServicesDetail->update([
            'title'    => $request->title,
            'description' => $request->description,
            'sequence'    => $request->sequence
        ]);

        $this->generateLog($request->user(), "updated this stat ({$taxServicesDetail->id}).");

        return response([
            'record' => $taxServicesDetail
        ]);
    }

    /**
     * StatService destroy
     * @param  Stat $stat
     * @param  Request $request
     * @return Response
     */
    public function destroy ($request,$taxServicesDetail): Response
    {
        $taxServicesDetail->delete();
        $this->generateLog($request->user(), "deleted this stat ({$taxServicesDetail->id}).");

        return response([
            'record' => 'Stat deleted'
        ]);
    }
}
