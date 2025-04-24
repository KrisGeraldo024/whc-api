<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\HearingAid;
use App\Services\HearingAid\HearingAidService;

class HearingAidController extends Controller
{
    /**
     * @var HearingAidService
     */
    protected $hearingAidService;

    /**
     * HearingAidController constructor
     * @param HearingAidService $hearingAidService
     */
    public function __construct (HearingAidService $hearingAidService)
    {
        $this->hearingAidService = $hearingAidService;
    }

    /**
     * HearingAidController index
     * @param  Request  $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->hearingAidService->index($request);
    }

    /**
     * HearingAidController store
     * @param  Request  $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->hearingAidService->store($request);
    }

    /**
     * HearingAidController show
     * @param  HearingAid $hearing_aid
     * @return Response
     */
    public function show (HearingAid $hearing_aid, Request $request): Response
    {
        return $this->hearingAidService->show($hearing_aid, $request);
    }

    /**
     * HearingAidController update
     * @param  HearingAid $hearing_aid
     * @param  Request $request
     * @return Response
     */
    public function update (HearingAid $hearing_aid, Request $request): Response
    {
        return $this->hearingAidService->update($hearing_aid, $request);
    }

    /**
     * HearingAidController destroy
     * @param  HearingAid $hearing_aid
     * @param  Request $request
     * @return Response
     */
    public function destroy (HearingAid $hearing_aid, Request $request): Response
    {
        return $this->hearingAidService->destroy($hearing_aid, $request);
    }
}
