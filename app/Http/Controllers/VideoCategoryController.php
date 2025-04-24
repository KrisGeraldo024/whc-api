<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\VideoCategory;
use App\Services\Video\VideoCategoryService;

class VideoCategoryController extends Controller
{
    /**
     * @var VideoCategoryService
     */
    protected $videoCategoryService;

    /**
     * VideoCategoryController constructor
     * @param VideoCategoryService $videoCategoryService
     */
    public function __construct (VideoCategoryService $videoCategoryService)
    {
        $this->videoCategoryService = $videoCategoryService;
    }

    /**
     * VideoCategoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->videoCategoryService->index($request);
    }

    /**
     * VideoCategoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->videoCategoryService->store($request);
    }

    /**
     * VideoCategoryController show
     * @param  VideoCategory $category
     * @param  Request $request
     * @return Response
     */
    public function show (VideoCategory $category, Request $request): Response
    {
        return $this->videoCategoryService->show($category, $request);
    }

    /**
     * VideoCategoryController update
     * @param  VideoCategory $category
     * @param  Request $request
     * @return Response
     */
    public function update (VideoCategory $category, Request $request): Response
    {
        return $this->videoCategoryService->update($category, $request);
    }

    /**
     * VideoCategoryController destroy
     * @param  VideoCategory $category
     * @param  Request $request
     * @return Response
     */
    public function destroy (VideoCategory $category, Request $request): Response
    {
        return $this->videoCategoryService->destroy($category, $request);
    }
}
