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
    PageTag
};
use App\Services\Page\PageTagService;

class PageTagController extends Controller
{
    /**
     * @var PageTagService
    */
    protected $pageTagService;

    /**
     * PageTag constructor
     * @param PageTagService $pageTagService
    */
    public function __construct (PageTagService $pageTagService)    {
        $this->pageTagService = $pageTagService;
    }

    /**
     * PageTagController index
     * @param Request $request
     * @param Page $page
     * @return Response
     */

    public function index(Request $request, $pageIdentifier): Response
    {
        return $this->pageTagService->index($request, $pageIdentifier);
    }

    /**
     * PageTagController tags
     * @param Request $request
     * @param Page $pageIdentifier
     * @return Response
     */

     public function getTagList(Request $request, $pageIdentifier): Response
     {
         return $this->pageTagService->getTagList($request, $pageIdentifier);
     }


    /**
     * PageTagController store
     * @param Request $request
     * @param Page $pageIdentifier
     * @return Response
     */
    public function store(Request $request, $pageIdentifier)
    {
        return $this->pageTagService->store($request, $pageIdentifier);
    }

    /**
     * PageTagController show
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageTag $tag
     * @return Response
     */
    public function show(Request $request, $pageIdentifier, PageTag $tag): Response
    {
        return $this->pageTagService->show($request, $pageIdentifier, $tag);
    }

    /**
     * PageTagController update
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageTag $tag
     * @return Response
     */
    public function update(Request $request, $pageIdentifier, PageTag $tag): Response
    {
        return $this->pageTagService->update($request, $pageIdentifier, $tag);
    }

    /**
     * PageTagController destroy
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageTag $tag
     * @return Response
     */
    public function destroy(Request $request, $pageIdentifier, PageTag $tag): Response
    {
        return $this->pageTagService->destroy($request, $pageIdentifier, $tag);
    }
}