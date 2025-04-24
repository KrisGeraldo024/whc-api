<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};

use App\Models\{
    taxServices,
};
use App\Services\taxonomies\TaxServicesServices;

class TaxServicesController extends Controller
{
    /**
     * @var TaxServicesServices
     */
    protected $taxServicesServices;

    /**
     * TaxServicesServices constructor
     * @param TaxServicesServices $taxServicesServices
     */
    public function __construct (TaxServicesServices $taxServicesServices)
    {
        $this->taxServicesServices = $taxServicesServices;
    }

    /**
     * HistoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->taxServicesServices->index($request);
    }

    /**
     * HistoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->taxServicesServices->store($request);
    }

    /**
     * HistoryController show
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function show (Request $request, taxServices $taxService): Response
    {
        return $this->taxServicesServices->show($request, $taxService);
    }

    /**
     * HistoryController update
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function update (Request $request, taxServices $taxService): Response
    {
        return $this->taxServicesServices->update($request, $taxService);
    }

    /**
     * HistoryController destroy
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function destroy (Request $request, taxServices $taxService): Response
    {
        return $this->taxServicesServices->destroy( $request, $taxService);
    }
}
