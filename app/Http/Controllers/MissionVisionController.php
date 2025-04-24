<?php

namespace App\Http\Controllers;

use App\Models\MissionVision;
use Illuminate\Http\{
    Request,
    Response
};


use App\Services\Philosophy\MissionVisionService;

class MissionVisionController extends Controller
{
   /**
     * @var HistoryService
     */
    protected $missionVisionService;

    /**
     * HistoryController constructor
     * @param HistoryService $historyService
     */
    public function __construct (MissionVisionService $missionVisionService)
    {
        $this->missionVisionService = $missionVisionService;
    }

    /**
     * HistoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->missionVisionService->index($request);
    }

    /**
     * HistoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->missionVisionService->store($request);
    }

    /**
     * HistoryController show
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function show (MissionVision $mission_vision, Request $request): Response
    {
        return $this->missionVisionService->show($mission_vision, $request);
    }

    /**
     * HistoryController update
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function update (MissionVision $mission_vision, Request $request): Response
    {
        return $this->missionVisionService->update($mission_vision, $request);
    }

    /**
     * HistoryController destroy
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function destroy (MissionVision $mission_vision, Request $request): Response
    {
        return $this->missionVisionService->destroy($mission_vision, $request);
    }
}
