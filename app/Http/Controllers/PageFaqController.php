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
    PageFaq
};
use App\Services\Page\PageFaqService;

class PageFaqController extends Controller
{
    /**
     * @var PageFaqService
     */
    protected $pageFaqService;

    /**
     * PageFaqController constructor
     * @param PageFaqService $pageFaqService
     */
    public function __construct (PageFaqService $pageFaqService)
    {
        $this->pageFaqService = $pageFaqService;
    }

    /**
     * PageFaqController index
     * @param Request $request
     * @param Page $pageIdentifier
     * @return Response
     */
    public function index(Request $request, $pageIdentifier): Response
    {
        return $this->pageFaqService->index($request, $pageIdentifier);
    }

    /**
     * PageFaqController store
     * @param Request $request
     * @param Page $pageIdentifier
     * @return Response
     */
    public function store(Request $request, $pageIdentifier): Response
    {
        return $this->pageFaqService->store($request, $pageIdentifier);
    }

    /**
     * PageFaqController show
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageFaq $faq
     * @return Response
     */
    public function show(Request $request, $pageIdentifier, PageFaq $faq): Response
    {
        return $this->pageFaqService->show($request, $pageIdentifier, $faq);
    }

    /**
     * PageFaqController update
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageFaq $faq
     * @return Response
     */
    public function update(Request $request, $pageIdentifier, PageFaq $faq): Response
    {
        return $this->pageFaqService->update($request, $pageIdentifier, $faq);
    }

    /**
     * PageFaqController destroy
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageFaq $faq
     * @return Response
     */
    public function destroy(Request $request, $pageIdentifier, PageFaq $faq): Response
    {
        return $this->pageFaqService->destroy($request, $pageIdentifier, $faq);
    }
}