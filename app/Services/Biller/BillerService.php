<?php

namespace App\Services\Biller;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Biller;
use App\Traits\GlobalTrait;

class BillerService
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
        $records = Biller::orderBy('created_at')
        ->with(['projects'])
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
        $record = Biller::create([
            'project_id'            => $request->project_id,
            'company_name'          => $request->company_name,
            'bpi_biller_name'       => $request->bpi_biller_name,
            'gcash_biller_name'     => $request->gcash_biller_name,
        ]);


        $this->generateLog($request->user(), "added this biller ({$record->id}).");
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
    public function show ($biller, $request): Response
    {
        $this->generateLog($request->user(), "viewed this biller ({$biller->id}).");
        return response([
            'record' => $biller
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($biller, $request): Response
    {
        $biller->update([
          'project_id'            => $request->project_id,
          'company_name'          => $request->company_name,
          'bpi_biller_name'       => $request->bpi_biller_name,
          'gcash_biller_name'     => $request->gcash_biller_name,
        ]);

        $this->generateLog($request->user(), "updated this biller ({$biller->id}).");
        return response([
            'record' => $biller
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($biller, $request): Response
    {
        $biller->delete();
        $this->generateLog($request->user(), "deleted this biller ({$biller->id}).");
        return response([
            'record' => 'Biller deleted'
        ]);
    }
}
