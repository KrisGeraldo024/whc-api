<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};

use App\Models\{
    PageUvp,
    UvpDetails
};
use App\Services\Page\UvpDetailsService;

class UvpDetailsController extends Controller
{
    protected $uvpDetailsService;

    /**
     * HistoryController constructor
     * @param HistoryService $historyService
     */
    public function __construct (UvpDetailsService $uvpDetailsService)
    {
        $this->uvpDetailsService = $uvpDetailsService;
    }

    /**
     * HistoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->uvpDetailsService->index($request);
    }

    /**
     * HistoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->uvpDetailsService->store($request);
    }

    /**
     * HistoryController show
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function show (Request $request, UvpDetails $uvpDetail): Response
    {
        return $this->uvpDetailsService->show($request, $uvpDetail);
    }

    /**
     * HistoryController update
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function update (Request $request, UvpDetails $uvpDetail): Response
    {
        return $this->uvpDetailsService->update($request, $uvpDetail);
    }

    /**
     * HistoryController destroy
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function destroy (Request $request, UvpDetails $uvpDetail): Response
    {
        return $this->uvpDetailsService->destroy( $request, $uvpDetail);
    }
}
