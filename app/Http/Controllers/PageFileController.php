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
    PageFile
};
use App\Services\Page\PageFileService;

class PageFileController extends Controller
{
    /**
     * @var PageFileService
    */
    protected $pageFileService;

    /**
     * PageFile constructor
     * @param PageFileService $pageFileService
    */
    public function __construct (PageFileService $pageFileService)    {
        $this->pageFileService = $pageFileService;
    }

    /**
     * PageFileController index
     * @param Request $request
     * @param Page $pageIdentifier
     * @return Response
     */

    public function index(Request $request, $pageIdentifier): Response
    {
        return $this->pageFileService->index($request, $pageIdentifier);
    }


    /**
     * PageFileController store
     * @param Request $request
     * @param Page $pageIdentifier
     * @return Response
     */
    public function store(Request $request, $pageIdentifier)
    {
        return $this->pageFileService->store($request, $pageIdentifier);
    }
 
    /**
     * PageFileController show
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageFile $file
     * @return Response
     */
    public function show(Request $request, $pageIdentifier, PageFile $file): Response
    {
        return $this->pageFileService->show($request, $pageIdentifier, $file);
    }

    /**
     * PageFileController update
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageFile $file
     * @return Response
     */
    public function update(Request $request, $pageIdentifier, PageFile $file): Response
    {
        return $this->pageFileService->update($request, $pageIdentifier, $file);
    }

    /**
     * PageFileController destroy
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageFile $file
     * @return Response
     */
    public function destroy(Request $request, $pageIdentifier, PageFile $file): Response
    {
        return $this->pageFileService->destroy($request, $pageIdentifier, $file);
    }
}