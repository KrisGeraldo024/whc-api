<?php

namespace App\Http\Controllers;

use App\Models\ConstructionUpdate;
use Illuminate\Http\{
    Request,
    Response
};

use App\Services\Project\ConstructionUpdateService;

class ConstructionUpdateController extends Controller
{
    protected $constructionUpdateService;

    /**
     * HistoryController constructor
     * @param HistoryService $historyService
     */
    public function __construct (ConstructionUpdateService $constructionUpdateService)
    {
        $this->constructionUpdateService = $constructionUpdateService;
    }

    /**
     * HistoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->constructionUpdateService->index($request);
    }

    /**
     * HistoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->constructionUpdateService->store($request);
    }

    /**
     * HistoryController show
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function show (constructionUpdate $construction_update, Request $request): Response
    {
        return $this->constructionUpdateService->show($construction_update, $request);
    }

    /**
     * HistoryController update
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function update (constructionUpdate $construction_update, Request $request): Response
    {
        return $this->constructionUpdateService->update($construction_update, $request);
    }

    /**
     * HistoryController destroy
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function destroy (constructionUpdate $construction_update, Request $request): Response
    {
        return $this->constructionUpdateService->destroy($construction_update, $request);
    }
}
