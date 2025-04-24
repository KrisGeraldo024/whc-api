<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\BranchRegion;
use App\Services\Branch\BranchRegionService;

class BranchRegionController extends Controller
{
    /**
     * @var BranchRegionService
     */
    protected $branchRegionService;

    /**
     * BranchRegionController constructor
     * @param BranchRegionService $branchRegionService
     */
    public function __construct (BranchRegionService $branchRegionService)
    {
        $this->branchRegionService = $branchRegionService;
    }

    /**
     * BranchRegionController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->branchRegionService->index($request);
    }

    /**
     * BranchRegionController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->branchRegionService->store($request);
    }

    /**
     * BranchRegionController show
     * @param  BranchRegion $region
     * @param  Request $request
     * @return Response
     */
    public function show (BranchRegion $region, Request $request): Response
    {
        return $this->branchRegionService->show($region, $request);
    }

    /**
     * BranchRegionController update
     * @param  BranchRegion $region
     * @param  Request $request
     * @return Response
     */
    public function update (BranchRegion $region, Request $request): Response
    {
        return $this->branchRegionService->update($region, $request);
    }

    /**
     * BranchRegionController destroy
     * @param  BranchRegion $region
     * @param  Request $request
     * @return Response
     */
    public function destroy (BranchRegion $region, Request $request): Response
    {
        return $this->branchRegionService->destroy($region, $request);
    }
}
