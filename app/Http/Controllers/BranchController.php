<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\Branch;
use App\Services\Branch\BranchService;

class BranchController extends Controller
{
    /**
     * @var BranchService
     */
    protected $branchService;

    /**
     * BranchController constructor
     * @param BranchService $branchService
     */
    public function __construct (BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    /**
     * BranchController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->branchService->index($request);
    }

    /**
     * BranchController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->branchService->store($request);
    }

    /**
     * BranchController show
     * @param  Branch $branch
     * @param  Request $request
     * @return Response
     */
    public function show (Branch $branch, Request $request): Response
    {
        return $this->branchService->show($branch, $request);
    }

    /**
     * BranchController update
     * @param  Branch $branch
     * @param  Request $request
     * @return Response
     */
    public function update (Branch $branch, Request $request): Response
    {
        return $this->branchService->update($branch, $request);
    }

    /**
     * BranchController destroy
     * @param  Branch $branch
     * @param  Request $request
     * @return Response
     */
    public function destroy (Branch $branch, Request $request): Response
    {
        return $this->branchService->destroy($branch, $request);
    }
}
