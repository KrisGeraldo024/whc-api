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
    PageCard
};
use App\Services\Page\PageCardService;

class PageCardController extends Controller
{
    /**
     * @var PageCardService
     */
    protected $pageCardService;

    /**
     * PageCardController constructor
     * @param PageCardService $pageCardService
     */
    public function __construct (PageCardService $pageCardService)
    {
        $this->pageCardService = $pageCardService;
    }

    /**
     * PageCardController index
     * @param Request $request
     * @param Page $pageIdentifier
     * @return Response
     */
    public function index(Request $request, $pageIdentifier): Response
    {
        return $this->pageCardService->index($request, $pageIdentifier);
    }

    public function getLastSeq(Request $request, $pageIdentifier): Response
    {
        return $this->pageCardService->getLastSeq($request, $pageIdentifier);
    }

    /**
     * PageCardController store
     * @param Request $request
     * @param Page $pageIdentifier
     * @return Response
     */
    public function store(Request $request, $pageIdentifier): Response
    {
        return $this->pageCardService->store($request, $pageIdentifier);
    }

    /**
     * PageCardController show
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageCard $card
     * @return Response
     */
    public function show(Request $request, $pageIdentifier, PageCard $card): Response
    {
        return $this->pageCardService->show($request, $pageIdentifier, $card);
    }

    /**
     * PageCardController update
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageCard $card
     * @return Response
     */
    public function update(Request $request, $pageIdentifier, PageCard $card): Response
    {
        return $this->pageCardService->update($request, $pageIdentifier, $card);
    }

    /**
     * PageCardController destroy
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageCard $card
     * @return Response
     */
    public function destroy(Request $request, $pageIdentifier, PageCard $card): Response
    {
        return $this->pageCardService->destroy($request, $pageIdentifier, $card);
    }
}