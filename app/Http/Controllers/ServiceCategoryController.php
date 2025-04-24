<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\ServiceCategory;
use App\Services\Service\ServiceCategoryService;

class ServiceCategoryController extends Controller
{
    /**
     * @var ServiceCategoryService
     */
    protected $serviceCategoryService;

    /**
     * ServiceCategoryController constructor
     * @param ServiceCategoryService $serviceCategoryService
     */
    public function __construct (ServiceCategoryService $serviceCategoryService)
    {
        $this->serviceCategoryService = $serviceCategoryService;
    }

    /**
     * ServiceCategoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->serviceCategoryService->index($request);
    }

    /**
     * ServiceCategoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->serviceCategoryService->store($request);
    }

    /**
     * ServiceCategoryController show
     * @param  ServiceCategory $category
     * @param  Request $request
     * @return Response
     */
    public function show (ServiceCategory $category, Request $request): Response
    {
        return $this->serviceCategoryService->show($category, $request);
    }

    /**
     * ServiceCategoryController update
     * @param  ServiceCategory $category
     * @param  Request $request
     * @return Response
     */
    public function update (ServiceCategory $category, Request $request): Response
    {
        return $this->serviceCategoryService->update($category, $request);
    }

    /**
     * ServiceCategoryController destroy
     * @param  ServiceCategory $category
     * @param  Request $request
     * @return Response
     */
    public function destroy (ServiceCategory $category, Request $request): Response
    {
        return $this->serviceCategoryService->destroy($category, $request);
    }
}
