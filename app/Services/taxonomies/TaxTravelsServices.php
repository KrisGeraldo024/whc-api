<?php

namespace App\Services\taxonomies;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\TaxTravels;
use App\Traits\GlobalTrait;

class TaxTravelsServices
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

        $records = TaxTravels::orderBy('sequence')
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

        $record = TaxTravels::create($request->all());

        $this->generateLog($request->user(), "added this stat ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * StatService show
     * @param  TaxTravels $taxTravel
     * @param  Request $request
     * @return Response
     */
    public function show ( $request,$taxTravel): Response
    {
        $this->generateLog($request->user(), "viewed this stat ({$taxTravel->id}).");
        
        return response([
            'record' => $taxTravel
        ]);
    }

    /**
     * StatService update
     * @param  TaxTravels $taxTravel
     * @param  Request $request
     * @return Response
     */
    public function update ($request,$taxTravel): Response
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $taxTravel->update([
            'title'    => $request->title,
            'description' => $request->description,
            'sequence'    => $request->sequence
        ]);

        $this->generateLog($request->user(), "updated this stat ({$taxTravel->id}).");

        return response([
            'record' => $taxTravel
        ]);
    }

    /**
     * StatService destroy
     * @param  TaxTravels $stat
     * @param  Request $request
     * @return Response
     */
    public function destroy ($request,$taxTravel): Response
    {
        $taxTravel->delete();
        $this->generateLog($request->user(), "deleted this stat ({$taxTravel->id}).");

        return response([
            'record' => 'Stat deleted'
        ]);
    }
}
