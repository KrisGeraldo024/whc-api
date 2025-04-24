<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\History;
use App\Services\History\HistoryService;
use App\Http\Requests\HistoryRequest;

class HistoryController extends Controller
{
    /**
     * @var HistoryService
     */
    protected $historyService;

    /**
     * HistoryController constructor
     * @param HistoryService $historyService
     */
    public function __construct (HistoryService $historyService)
    {
        $this->historyService = $historyService;
    }

    /**
     * HistoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->historyService->index($request);
    }

    /**
     * HistoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (HistoryRequest $request): Response
    {
        return $this->historyService->store($request);
    }

    /**
     * HistoryController show
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function show (History $history, Request $request): Response
    {
        return $this->historyService->show($history, $request);
    }

    /**
     * HistoryController update
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function update (History $history, HistoryRequest $request): Response
    {
        return $this->historyService->update($history, $request);
    }

    /**
     * HistoryController destroy
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function destroy (History $history, Request $request): Response
    {
        return $this->historyService->destroy($history, $request);
    }
}
