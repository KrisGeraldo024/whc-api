<?php

namespace App\Http\Controllers;


use Illuminate\Http\{
    Request,
    Response
};

use App\Models\{
    TaxRemittances,
};
use App\Services\taxonomies\TaxRemittancesServices;

class TaxRemittancesController extends Controller
{
     /**
     * @var TaxRemittancesServices
     */
    protected $taxRemittancesServices;

    /**
     * TaxRemittancesServices constructor
     * @param TaxRemittancesServices $taxRemittancesServices
     */
    public function __construct (TaxRemittancesServices $taxRemittancesServices)
    {
        $this->taxRemittancesServices = $taxRemittancesServices;
    }

    /**
     * HistoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->taxRemittancesServices->index($request);
    }

    /**
     * HistoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->taxRemittancesServices->store($request);
    }

    /**
     * HistoryController show
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function show (Request $request, TaxRemittances $taxRemittance): Response
    {
        return $this->taxRemittancesServices->show($request, $taxRemittance);
    }

    /**
     * HistoryController update
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function update (Request $request, TaxRemittances $taxRemittance): Response
    {
        return $this->taxRemittancesServices->update($request, $taxRemittance);
    }

    /**
     * HistoryController destroy
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function destroy (Request $request, TaxRemittances $taxRemittance): Response
    {
        return $this->taxRemittancesServices->destroy( $request, $taxRemittance);
    }
}
