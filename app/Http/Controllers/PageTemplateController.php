<?php

namespace App\Http\Controllers;

use App\Models\PageTemplate;
use Illuminate\Http\{
    Request,
    Response
};

use App\Services\PageTemplate\PageTemplateService;

class PageTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $pageTemplateService;

    public function __construct (PageTemplateService $pageTemplateService)
    {
        $this->pageTemplateService = $pageTemplateService;
    }

    public function index (Request $request): Response
    {
        return $this->pageTemplateService->index($request);
    }

    public function store (Request $request): Response
    {
        return $this->pageTemplateService->store($request);
    }

    public function show (Request $request, PageTemplate $pageTemplate): Response
    {
        return $this->pageTemplateService->show($request,$pageTemplate);
    }
    
    public function update (Request $request, PageTemplate $pageTemplate): Response
    {
        return $this->pageTemplateService->update($request,$pageTemplate);
    }
    
    public function destroy (Request $request,PageTemplate $pageTemplate): Response
    {
        return $this->pageTemplateService->destroy($request,$pageTemplate);
    }
}
