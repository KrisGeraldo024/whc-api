<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};

use App\Models\{
    taxServicesDetails,
};
use App\Services\taxonomies\TaxServicesDetailsServices;


class TaxServicesDetailsController extends Controller
{
    protected $taxServicesDetailsServices;

    /**
     * HistoryController constructor
     * @param HistoryService $historyService
     */
    public function __construct (TaxServicesDetailsServices $taxServicesDetailsServices)
    {
        $this->taxServicesDetailsServices = $taxServicesDetailsServices;
    }

    /**
     * HistoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->taxServicesDetailsServices->index($request);
    }

    /**
     * HistoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->taxServicesDetailsServices->store($request);
    }

    /**
     * HistoryController show
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function show (Request $request, taxServicesDetails $taxServicesDetail): Response
    {
        return $this->taxServicesDetailsServices->show($request, $taxServicesDetail);
    }

    /**
     * HistoryController update
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function update (Request $request, taxServicesDetails $taxServicesDetail): Response
    {
        return $this->taxServicesDetailsServices->update($request, $taxServicesDetail);
    }

    /**
     * HistoryController destroy
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function destroy (Request $request, taxServicesDetails $taxServicesDetail): Response
    {
        return $this->taxServicesDetailsServices->destroy( $request, $taxServicesDetail);
    }
}
