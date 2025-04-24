<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};

use App\Models\{
    TaxTelcos,
};
use App\Services\taxonomies\TaxTelcosServices;

class TaxTelcosController extends Controller
{
/**
     * @var TaxTelcosServices
     */
    protected $taxTelcosServices;

    /**
     * TaxRemittancesServices constructor
     * @param TaxTelcosServices $taxRemittancesServices
     */
    public function __construct (TaxTelcosServices $taxTelcosServices)
    {
        $this->taxTelcosServices = $taxTelcosServices;
    }

    /**
     * HistoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->taxTelcosServices->index($request);
    }

    /**
     * HistoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->taxTelcosServices->store($request);
    }

    /**
     * HistoryController show
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function show (Request $request, TaxTelcos $taxTelco): Response
    {
        return $this->taxTelcosServices->show($request, $taxTelco);
    }

    /**
     * HistoryController update
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function update (Request $request, TaxTelcos $taxTelco): Response
    {
        return $this->taxTelcosServices->update($request, $taxTelco);
    }

    /**
     * HistoryController destroy
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function destroy (Request $request, TaxTelcos $taxTelco): Response
    {
        return $this->taxTelcosServices->destroy( $request, $taxTelco);
    }
}
