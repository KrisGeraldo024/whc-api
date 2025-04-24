<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};

use App\Models\{
    TaxTravels,
};
use App\Services\taxonomies\TaxTravelsServices;

class TaxTravelsController extends Controller
{
    /**
     * @var TaxTravelsServices
     */
    protected $taxTravelsServices;

    /**
     * TaxRemittancesServices constructor
     * @param TaxTravelsServices $taxRemittancesServices
     */
    public function __construct (TaxTravelsServices $taxTravelsServices)
    {
        $this->taxTravelsServices = $taxTravelsServices;
    }

    /**
     * HistoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->taxTravelsServices->index($request);
    }

    /**
     * HistoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->taxTravelsServices->store($request);
    }

    /**
     * HistoryController show
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function show (Request $request, TaxTravels $taxTravel): Response
    {
        return $this->taxTravelsServices->show($request, $taxTravel);
    }

    /**
     * HistoryController update
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function update (Request $request, TaxTravels $taxTravel): Response
    {
        return $this->taxTravelsServices->update($request, $taxTravel);
    }

    /**
     * HistoryController destroy
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function destroy (Request $request, TaxTravels $taxTravel): Response
    {
        return $this->taxTravelsServices->destroy( $request, $taxTravel);
    }
}
