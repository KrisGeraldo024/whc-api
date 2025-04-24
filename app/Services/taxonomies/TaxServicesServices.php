<?php

namespace App\Services\taxonomies;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\taxServices;
use App\Traits\GlobalTrait;

class TaxServicesServices
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
        $records = taxServices::orderBy('sequence')
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

        $record = taxServices::create($request->all());

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
    public function show ( $request,$taxService): Response
    {
        $this->generateLog($request->user(), "viewed this stat ({$taxService->id}).");
        
        return response([
            'record' => $taxService
        ]);
    }

    /**
     * StatService update
     * @param  taxServices $taxservices
     * @param  Request $request
     * @return Response
     */
    public function update ($request,$taxService): Response
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $taxService->update([
            'title'    => $request->title,
            'description' => $request->description,
            'sequence'    => $request->sequence
        ]);

        $this->generateLog($request->user(), "updated this stat ({$taxService->id}).");

        return response([
            'record' => $taxService
        ]);
    }

    /**
     * StatService destroy
     * @param  Stat $stat
     * @param  Request $request
     * @return Response
     */
    public function destroy ($request,$taxService): Response
    {
        $taxService->delete();
        $this->generateLog($request->user(), "deleted this stat ({$taxService->id}).");

        return response([
            'record' => 'Stat deleted'
        ]);
    }
}
