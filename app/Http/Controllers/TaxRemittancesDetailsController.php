<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};

use App\Models\{
    TaxRemittancesDetails,
};
use App\Services\taxonomies\TaxRemittancesDetailsServices;

class TaxRemittancesDetailsController extends Controller
{
    protected $taxRemittancesDetailsServices;

    /**
     * HistoryController constructor
     * @param HistoryService $historyService
     */
    public function __construct (TaxRemittancesDetailsServices $taxRemittancesDetailsServices)
    {
        $this->taxRemittancesDetailsServices = $taxRemittancesDetailsServices;
    }

    /**
     * HistoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->taxRemittancesDetailsServices->index($request);
    }

    /**
     * HistoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->taxRemittancesDetailsServices->store($request);
    }

    /**
     * HistoryController show
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function show (Request $request, TaxRemittancesDetails $taxRemittancesDetail): Response
    {
        return $this->taxRemittancesDetailsServices->show($request, $taxRemittancesDetail);
    }

    /**
     * HistoryController update
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function update (Request $request, TaxRemittancesDetails $taxRemittancesDetail): Response
    {
        return $this->taxRemittancesDetailsServices->update($request, $taxRemittancesDetail);
    }

    /**
     * HistoryController destroy
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function destroy (Request $request, TaxRemittancesDetails $taxRemittancesDetail): Response
    {
        return $this->taxRemittancesDetailsServices->destroy( $request, $taxRemittancesDetail);
    }
}
