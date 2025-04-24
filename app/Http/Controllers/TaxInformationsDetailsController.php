<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};

use App\Models\{
    TaxInformationsDetails,
};
use App\Services\taxonomies\TaxInformationsDetailsServices;

class TaxInformationsDetailsController extends Controller
{
    protected $taxInformationsDetailsServices;

    /**
     * HistoryController constructor
     * @param HistoryService $historyService
     */
    public function __construct (TaxInformationsDetailsServices $taxInformationsDetailsServices)
    {
        $this->taxInformationsDetailsServices = $taxInformationsDetailsServices;
    }

    /**
     * HistoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->taxInformationsDetailsServices->index($request);
    }

    /**
     * HistoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->taxInformationsDetailsServices->store($request);
    }

    /**
     * HistoryController show
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function show (Request $request, TaxInformationsDetails $taxInformationsDetail): Response
    {
        return $this->taxInformationsDetailsServices->show($request, $taxInformationsDetail);
    }

    /**
     * HistoryController update
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function update (Request $request, TaxInformationsDetails $taxInformationsDetail): Response
    {
        return $this->taxInformationsDetailsServices->update($request, $taxInformationsDetail);
    }

    /**
     * HistoryController destroy
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function destroy (Request $request, TaxInformationsDetails $taxInformationsDetail): Response
    {
        return $this->taxInformationsDetailsServices->destroy( $request, $taxInformationsDetail);
    }
}
