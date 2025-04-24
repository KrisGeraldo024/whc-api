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
use App\Models\Page;
use App\Services\Page\PageService;

class PageController extends Controller
{
    /**
     * @var PageService
     */
    protected $pageService;

    /**
     * PageController constructor
     * @param PageService $pageService
     */
    public function __construct (PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * PageController index
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->pageService->index($request);
    }

    /**
     * PageController store
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        return $this->pageService->store($request);
    }

    /**
     * PageController show
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function show(Request $request, Page $page): Response
    {
        return $this->pageService->show($request, $page);
    }

    /**
     * PageController update
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function update(Page $page, Request $request): Response
    {
        return $this->pageService->update($page, $request);
    }

    /**
     * PageController getLandingPage
     * @param Request $request
     * @param string $identifier
     * @return Response
     */
    // public function getLandingPage(Request $request, string $identifier): Response
    // {
    //     return $this->pageService->getLandingPage($request, $identifier);
    // }

    /**
     * PageController generalSearch
     * @param Request $request
     * @return Response
     */
    // public function generalSearch(Request $request): Response
    // {
    //     return $this->pageService->generalSearch($request);
    // }


    public function pageData (string $identifier, Request $request): Response
    {
        return $this->pageService->pageData($identifier, $request);
    }

    // public function billerSearch (Request $request): Response
    // {
    //     return $this->pageService->billerSearch($request);
    // }

    // public function projectSearch (Request $request): Response
    // {
    //     return $this->pageService->projectSearch($request);
    // }

    // public function documentSearch (Request $request): Response
    // {
    //     return $this->pageService->documentSearch($request);
    // }


    /**
     * PageController sitemap
     * @param Request $request
     * @return Response
     */
    public function sitemap(Request $request): Response
    {
        return $this->pageService->sitemap($request);
    }

    public function getCategories (Request $request): Response
    {
        return $this->pageService->getCategories($request);
    }
}