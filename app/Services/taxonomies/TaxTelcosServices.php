<?php

namespace App\Services\taxonomies;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\TaxTelcos;
use App\Traits\GlobalTrait;

class TaxTelcosServices
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

        $records = TaxTelcos::orderBy('sequence')
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

        $record = TaxTelcos::create($request->all());

        $this->generateLog($request->user(), "added this stat ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * StatService show
     * @param  TaxTelcos $taxTelcos
     * @param  Request $request
     * @return Response
     */
    public function show ( $request,$taxTelcos): Response
    {
        $this->generateLog($request->user(), "viewed this stat ({$taxTelcos->id}).");
        
        return response([
            'record' => $taxTelcos
        ]);
    }

    /**
     * StatService update
     * @param  TaxTelcos $taxTelcos
     * @param  Request $request
     * @return Response
     */
    public function update ($request,$taxTelcos): Response
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $taxTelcos->update([
            'title'    => $request->title,
            'description' => $request->description,
            'sequence'    => $request->sequence
        ]);

        $this->generateLog($request->user(), "updated this stat ({$taxTelcos->id}).");

        return response([
            'record' => $taxTelcos
        ]);
    }

    /**
     * StatService destroy
     * @param  TaxTelcos $stat
     * @param  Request $request
     * @return Response
     */
    public function destroy ($request,$taxTelcos): Response
    {
        $taxTelcos->delete();
        $this->generateLog($request->user(), "deleted this stat ({$taxTelcos->id}).");

        return response([
            'record' => 'Stat deleted'
        ]);
    }
}
