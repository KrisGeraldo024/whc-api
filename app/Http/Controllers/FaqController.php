<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\Faq;
use App\Services\Faq\FaqService;

class FaqController extends Controller
{
    /**
     * @var FaqService
     */
    protected $faqService;

    /**
     * FaqController constructor
     * @param FaqService $faqService
     */
    public function __construct (FaqService $faqService)
    {
        $this->faqService = $faqService;
    }

    /**
     * FaqController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->faqService->index($request);
    }

    /**
     * FaqController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->faqService->store($request);
    }

    /**
     * FaqController show
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function show (Faq $faq, Request $request): Response
    {
        return $this->faqService->show($faq, $request);
    }

    /**
     * FaqController update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update (Faq $faq, Request $request): Response
    {
        return $this->faqService->update($faq, $request);
    }

    /**
     * FaqController destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy (Faq $faq, Request $request): Response
    {
        return $this->faqService->destroy($faq, $request);
    }
}
