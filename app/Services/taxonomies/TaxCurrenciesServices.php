<?php

namespace App\Services\taxonomies;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\TaxCurrencies;
use App\Traits\GlobalTrait;

class TaxCurrenciesServices
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

        $records = TaxCurrencies::orderBy('sequence')
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

        $record = TaxCurrencies::create($request->all());

        $this->generateLog($request->user(), "added this stat ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * StatService show
     * @param  TaxCurrencies $taxCurrency
     * @param  Request $request
     * @return Response
     */
    public function show ( $request,$taxCurrency): Response
    {
        $this->generateLog($request->user(), "viewed this stat ({$taxCurrency->id}).");
        
        return response([
            'record' => $taxCurrency
        ]);
    }

    /**
     * StatService update
     * @param  TaxCurrencies $taxCurrency
     * @param  Request $request
     * @return Response
     */
    public function update ($request,$taxCurrency): Response
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $taxCurrency->update([
            'title'    => $request->title,
            'description' => $request->description,
            'sequence'    => $request->sequence
        ]);

        $this->generateLog($request->user(), "updated this stat ({$taxCurrency->id}).");

        return response([
            'record' => $taxCurrency
        ]);
    }

    /**
     * StatService destroy
     * @param  TaxCurrencies $stat
     * @param  Request $request
     * @return Response
     */
    public function destroy ($request,$taxCurrency): Response
    {
        $taxCurrency->delete();
        $this->generateLog($request->user(), "deleted this stat ({$taxCurrency->id}).");

        return response([
            'record' => 'Stat deleted'
        ]);
    }
}
