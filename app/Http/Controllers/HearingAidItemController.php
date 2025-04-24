<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\{
    HearingAidItem,
    HearingAid
};
use App\Services\HearingAid\HearingAidItemService;

class HearingAidItemController extends Controller
{
    /**
     * @var HearingAidItemService
     */
    protected $hearingAidItemService;

    /**
     * HearingAidItemController constructor
     * @param HearingAidItemService $hearingAidItemService
     */
    public function __construct (HearingAidItemService $hearingAidItemService)
    {
        $this->hearingAidItemService = $hearingAidItemService;
    }

    /**
     * HearingAidItemController index
     * @param  HearingAid $hearing_aid
     * @param  Request $request
     * @return Response
     */
    public function index (HearingAid $hearing_aid, Request $request): Response
    {
        return $this->hearingAidItemService->index($hearing_aid, $request);
    }

    /**
     * HearingAidItemController store
     * @param  HearingAid $hearing_aid
     * @param  Request $request
     * @return Response
     */
    public function store (HearingAid $hearing_aid, Request $request): Response
    {
        return $this->hearingAidItemService->store($hearing_aid, $request);
    }

    /**
     * HearingAidItemController show
     * @param  HearingAid $hearing_aid
     * @param  HearingAidItem $item
     * @param  Request $request
     * @return Response
     */
    public function show (HearingAid $hearing_aid, HearingAidItem $item, Request $request): Response
    {
        return $this->hearingAidItemService->show($hearing_aid, $item, $request);
    }

    /**
     * HearingAidItemController update
     * @param  HearingAid $hearing_aid
     * @param  HearingAidItem $item
     * @param  Request $request
     * @return Response
     */
    public function update (HearingAid $hearing_aid, HearingAidItem $item, Request $request): Response
    {
        return $this->hearingAidItemService->update($hearing_aid, $item, $request);
    }

    /**
     * HearingAidItemController destroy
     * @param  HearingAid $hearing_aid
     * @param  HearingAidItem $item
     * @param  Request $request
     * @return Response
     */
    public function destroy (HearingAid $hearing_aid, HearingAidItem $item, Request $request): Response
    {
        return $this->hearingAidItemService->destroy($hearing_aid, $item, $request);
    }
}
