<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use Illuminate\Support\Facades\{
    Validator,
    Facade,
};
use App\Models\{
    Page,
    PageBanner
};
use App\Services\Page\PageBannerService;

class PageBannerController extends Controller
{
    /**
     * @var PageBannerService
     */
    protected $pageBannerService;

    /**
     * PageBannerController constructor
     * @param PageBannerService $pageBannerService
     */
    public function __construct (PageBannerService $pageBannerService)
    {
        $this->pageBannerService = $pageBannerService;
    }

    /**
     * PageBannerController index
     * @param Request $request
     * @param Page $pageIdentifier
     * @return Response
     */
    public function index(Request $request,  $pageIdentifier): Response
    {
        return $this->pageBannerService->index($request, $pageIdentifier);
    }

    /**
     * PageBannerController store
     * @param Request $request
     * @param Page $pageIdentifier
     * @return Response
     */
    public function store(Request $request,  $pageIdentifier): Response
    {
        return $this->pageBannerService->store($request, $pageIdentifier);
    }

    /**
     * PageBannerController show
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageBanner $banner
     * @return Response
     */
    public function show(Request $request,  $pageIdentifier, PageBanner $banner): Response
    {
        return $this->pageBannerService->show($request, $pageIdentifier, $banner);
    }

    /**
     * PageBannerController update
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageBanner $banner
     * @return Response
     */
    public function update(Request $request,  $pageIdentifier, PageBanner $banner): Response
    {
        return $this->pageBannerService->update($request, $pageIdentifier, $banner);
    }

    /**
     * PageBannerController destroy
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageBanner $banner
     * @return Response
     */
    public function destroy(Request $request,  $pageIdentifier, PageBanner $banner): Response
    {
        return $this->pageBannerService->destroy($request, $pageIdentifier, $banner);
    }
}
