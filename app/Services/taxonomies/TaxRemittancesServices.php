<?php

namespace App\Services\taxonomies;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\TaxRemittances;
use App\Traits\GlobalTrait;

class TaxRemittancesServices
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

        $records = TaxRemittances::orderBy('sequence')
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

        $record = TaxRemittances::create($request->all());

        $this->generateLog($request->user(), "added this stat ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * StatService show
     * @param  TaxRemittances $taxRemittance
     * @param  Request $request
     * @return Response
     */
    public function show ( $request,$taxRemittance): Response
    {
        $this->generateLog($request->user(), "viewed this stat ({$taxRemittance->id}).");
        
        return response([
            'record' => $taxRemittance
        ]);
    }

    /**
     * StatService update
     * @param  TaxRemittances $taxservices
     * @param  Request $request
     * @return Response
     */
    public function update ($request,$taxRemittance): Response
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $taxRemittance->update([
            'title'    => $request->title,
            'description' => $request->description,
            'sequence'    => $request->sequence
        ]);

        $this->generateLog($request->user(), "updated this stat ({$taxRemittance->id}).");

        return response([
            'record' => $taxRemittance
        ]);
    }

    /**
     * StatService destroy
     * @param  TaxRemittances $stat
     * @param  Request $request
     * @return Response
     */
    public function destroy ($request,$taxRemittance): Response
    {
        $taxRemittance->delete();
        $this->generateLog($request->user(), "deleted this stat ({$taxRemittance->id}).");

        return response([
            'record' => 'Stat deleted'
        ]);
    }
}
