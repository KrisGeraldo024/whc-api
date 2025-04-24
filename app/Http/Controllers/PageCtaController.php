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
    PageCta
};
use App\Services\Page\PageCtaService;

class PageCtaController extends Controller
{
    /**
     * @var PageCtaService
     */
    protected $pageCtaService;

    /**
     * PageCtaController constructor
     * @param PageCtaService $pageCtaService
     */
    public function __construct (PageCtaService $pageCtaService)
    {
        $this->pageCtaService = $pageCtaService;
    }

    /**
     * PageCtaController index
     * @param Request $request
     * @param Page $pageIdentifier
     * @return Response
     */
    public function index(Request $request, $pageIdentifier): Response
    {
        return $this->pageCtaService->index($request, $pageIdentifier);
    }

    /**
     * PageCtaController store
     * @param Request $request
     * @param Page $pageIdentifier
     * @return Response
     */
    public function store(Request $request, $pageIdentifier): Response
    {
        return $this->pageCtaService->store($request, $pageIdentifier);
    }

    /**
     * PageCtaController show
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageCta $cta
     * @return Response
     */
    public function show(Request $request, $pageIdentifier, PageCta $cta): Response
    {
        return $this->pageCtaService->show($request, $pageIdentifier, $cta);
    }

    /**
     * PageCtaController update
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageCta $cta
     * @return Response
     */
    public function update(Request $request, $pageIdentifier, PageCta $cta): Response
    {
        return $this->pageCtaService->update($request, $pageIdentifier, $cta);
    }

    /**
     * PageCtaController destroy
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageCta $cta
     * @return Response
     */
    public function destroy(Request $request, $pageIdentifier, PageCta $cta): Response
    {
        return $this->pageCtaService->destroy($request, $pageIdentifier, $cta);
    }
}