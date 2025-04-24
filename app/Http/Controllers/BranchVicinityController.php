<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\{
    BranchRegion,
    BranchVicinity
};
use App\Services\Branch\BranchVicinityService;

class BranchVicinityController extends Controller
{
    /**
     * @var BranchVicinityService
     */
    protected $branchVicinityService;

    /**
     * BranchVicinityController constructor
     * @param BranchVicinityService $branchVicinityService
     */
    public function __construct (BranchVicinityService $branchVicinityService)
    {
        $this->branchVicinityService = $branchVicinityService;
    }

    /**
     * BranchVicinityController index
     * @param  BranchRegion $region
     * @param  Request $request
     * @return Response
     */
    public function index (BranchRegion $region, Request $request): Response
    {
        return $this->branchVicinityService->index($region, $request);
    }

    /**
     * BranchVicinityController store
     * @param  BranchRegion $region
     * @param  Request $request
     * @return Response
     */
    public function store (BranchRegion $region, Request $request): Response
    {
        return $this->branchVicinityService->store($region, $request);
    }

    /**
     * BranchVicinityController show
     * @param  BranchRegion $region
     * @param  BranchVicinity $vicinity
     * @param  Request $request
     * @return Response
     */
    public function show (BranchRegion $region, BranchVicinity $vicinity, Request $request): Response
    {
        return $this->branchVicinityService->show($region, $vicinity, $request);
    }

    /**
     * BranchVicinityController update
     * @param  BranchRegion $region
     * @param  BranchVicinity $vicinity
     * @param  Request $request
     * @return Response
     */
    public function update (BranchRegion $region, BranchVicinity $vicinity, Request $request): Response
    {
        return $this->branchVicinityService->update($region, $vicinity, $request);
    }

    /**
     * BranchVicinityController destroy
     * @param  BranchRegion $region
     * @param  BranchVicinity $vicinity
     * @param  Request $request
     * @return Response
     */
    public function destroy (BranchRegion $region, BranchVicinity $vicinity, Request $request): Response
    {
        return $this->branchVicinityService->destroy($region, $vicinity, $request);
    }
}
