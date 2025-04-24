<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};

use App\Models\{
    TaxInformations,
};
use App\Services\taxonomies\TaxInformationsServices;

class TaxInformationsController extends Controller
{
    /**
     * @var TaxInformationsServices
     */
    protected $taxInformationsServices;

    /**
     * TaxRemittancesServices constructor
     * @param TaxInformationsServices $taxRemittancesServices
     */
    public function __construct (TaxInformationsServices $taxInformationsServices)
    {
        $this->taxInformationsServices = $taxInformationsServices;
    }

    /**
     * HistoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->taxInformationsServices->index($request);
    }

    /**
     * HistoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->taxInformationsServices->store($request);
    }

    /**
     * HistoryController show
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function show (Request $request, TaxInformations $taxInformation): Response
    {
        return $this->taxInformationsServices->show($request, $taxInformation);
    }

    /**
     * HistoryController update
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function update (Request $request, TaxInformations $taxInformation): Response
    {
        return $this->taxInformationsServices->update($request, $taxInformation);
    }

    /**
     * HistoryController destroy
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function destroy (Request $request, TaxInformations $taxInformation): Response
    {
        return $this->taxInformationsServices->destroy( $request, $taxInformation);
    }
}
