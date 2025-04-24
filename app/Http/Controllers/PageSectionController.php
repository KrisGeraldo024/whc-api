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
    PageSection
};
use App\Services\Page\PageSectionService;

class PageSectionController extends Controller
{
    /**
     * @var PageSectionService
     */
    protected $pageSectionService;

    /**
     * PageSectionController constructor
     * @param PageSectionService $pageSectionService
     */
    public function __construct (PageSectionService $pageSectionService)
    {
        $this->pageSectionService = $pageSectionService;
    }

    /**
     * PageSectionController index
     * @param Request $request
     * @param Page $pageIdentifier
     * @return Response
     */
    public function index(Request $request,  $pageIdentifier): Response
    {
        return $this->pageSectionService->index($request, $pageIdentifier);
    }

    /**
     * PageSectionController store
     * @param Request $request
     * @param Page $pageIdentifier
     * @return Response
     */
    public function store(Request $request,  $pageIdentifier): Response
    {
        return $this->pageSectionService->store($request, $pageIdentifier);
    }

    /**
     * PageSectionController show
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageSection $page_section
     * @return Response
     */
    public function show(Request $request,  PageSection $page_section): Response
    {
        return $this->pageSectionService->show($request, $page_section);
    }

    /**
     * PageSectionController update
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageSection $page_section
     * @return Response
     */
    public function update(Request $request, PageSection $page_section): Response
    {
        return $this->pageSectionService->update($request, $page_section);
    }

    /**
     * PageSectionController destroy
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageSection $page_section
     * @return Response
     */
    public function destroy(Request $request,  $pageIdentifier, PageSection $page_section): Response
    {
        return $this->pageSectionService->destroy($request, $pageIdentifier, $page_section);
    }
}
