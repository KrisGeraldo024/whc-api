<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\HearingAidCategory;
use App\Services\HearingAid\HearingAidCategoryService;

class HearingAidCategoryController extends Controller
{
    /**
     * @var HearingAidCategoryService
     */
    protected $hearingAidCategoryService;

    /**
     * HearingAidCategoryController constructor
     * @param HearingAidCategoryService $hearingAidCategoryService
     */
    public function __construct (HearingAidCategoryService $hearingAidCategoryService)
    {
        $this->hearingAidCategoryService = $hearingAidCategoryService;
    }

    /**
     * HearingAidCategoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->hearingAidCategoryService->index($request);
    }

    /**
     * HearingAidCategoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->hearingAidCategoryService->store($request);
    }

    /**
     * HearingAidCategoryController show
     * @param  HearingAidCategory $category
     * @param  Request $request
     * @return Response
     */
    public function show (HearingAidCategory $category, Request $request): Response
    {
        return $this->hearingAidCategoryService->show($category, $request);
    }

    /**
     * HearingAidCategoryController update
     * @param  HearingAidCategory $category
     * @param  Request $request
     * @return Response
     */
    public function update (HearingAidCategory $category, Request $request): Response
    {
        return $this->hearingAidCategoryService->update($category, $request);
    }

    /**
     * HearingAidCategoryController destroy
     * @param  HearingAidCategory $category
     * @param  Request $request
     * @return Response
     */
    public function destroy (HearingAidCategory $category, Request $request): Response
    {
        return $this->hearingAidCategoryService->destroy($category, $request);
    }
}
