<?php

namespace App\Services\taxonomies;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\TaxInformations;
use App\Traits\GlobalTrait;

class TaxInformationsServices
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

        $records = TaxInformations::orderBy('sequence')
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

        $record = TaxInformations::create($request->all());

        $this->generateLog($request->user(), "added this stat ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * StatService show
     * @param  TaxInformations $taxInformation
     * @param  Request $request
     * @return Response
     */
    public function show ( $request,$taxInformation): Response
    {
        $this->generateLog($request->user(), "viewed this stat ({$taxInformation->id}).");
        
        return response([
            'record' => $taxInformation
        ]);
    }

    /**
     * StatService update
     * @param  TaxInformations $taxservices
     * @param  Request $request
     * @return Response
     */
    public function update ($request,$taxInformation): Response
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $taxInformation->update([
            'title'    => $request->title,
            'description' => $request->description,
            'sequence'    => $request->sequence
        ]);

        $this->generateLog($request->user(), "updated this stat ({$taxInformation->id}).");

        return response([
            'record' => $taxInformation
        ]);
    }

    /**
     * StatService destroy
     * @param  TaxInformations $stat
     * @param  Request $request
     * @return Response
     */
    public function destroy ($request,$taxInformation): Response
    {
        $taxInformation->delete();
        $this->generateLog($request->user(), "deleted this stat ({$taxInformation->id}).");

        return response([
            'record' => 'Stat deleted'
        ]);
    }
}
