<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};

use App\Models\{
    TaxCurrencies,
};
use App\Services\taxonomies\TaxCurrenciesServices;

class TaxCurrenciesController extends Controller
{
    /**
     * @var TaxCurrenciesServices
     */
    protected $TaxCurrenciesServices;

    /**
     * TaxCurrenciesServices constructor
     * @param TaxCurrenciesServices $taxCurrenciesServices
     */
    public function __construct (TaxCurrenciesServices $taxCurrenciesServices)
    {
        $this->taxCurrenciesServices = $taxCurrenciesServices;
    }

    /**
     * HistoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->taxCurrenciesServices->index($request);
    }

    /**
     * HistoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->taxCurrenciesServices->store($request);
    }

    /**
     * HistoryController show
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function show (Request $request, TaxCurrencies $taxCurrency): Response
    {
        return $this->taxCurrenciesServices->show($request, $taxCurrency);
    }

    /**
     * HistoryController update
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function update (Request $request, TaxCurrencies $taxCurrency): Response
    {
        return $this->taxCurrenciesServices->update($request, $taxCurrency);
    }

    /**
     * HistoryController destroy
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function destroy (Request $request, TaxCurrencies $taxCurrency): Response
    {
        return $this->taxCurrenciesServices->destroy( $request, $taxCurrency);
    }
}
