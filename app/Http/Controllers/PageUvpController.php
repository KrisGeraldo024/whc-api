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
    PageUvp
};
use App\Services\Page\PageUvpService;

class PageUvpController extends Controller
{
    /**
     * @var PageUvpService
 */
    protected $pageUvpService;

    /**
     * PageUvp constructor
     * @param PageUvpService $pageUvpService
 */
    public function __construct (PageUvpService $pageUvpService)    {
        $this->pageUvpService = $pageUvpService;
    }

    /**
     * PageUvpController index
     * @param Request $request
     * @param Page $pageIdentifier
     * @return Response
     */

     public function index(Request $request, $pageIdentifier): Response
     {
         return $this->pageUvpService->index($request, $pageIdentifier);
     }


    /**
     * PageUvpController store
     * @param Request $request
     * @param Page $pageIdentifier
     * @return Response
     */
    public function store(Request $request, $pageIdentifier)
    {
        return $this->pageUvpService->store($request, $pageIdentifier);
    }

    /**
     * PageUvpController show
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageUvp $uvp
     * @return Response
     */
    public function show(Request $request, $pageIdentifier, PageUvp $uvp): Response
    {
        return $this->pageUvpService->show($request, $pageIdentifier, $uvp);
    }

    /**
     * PageUvpController update
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageUvp $uvp
     * @return Response
     */
    public function update(Request $request, $pageIdentifier, PageUvp $uvp): Response
    {
        return $this->pageUvpService->update($request, $pageIdentifier, $uvp);
    }

    /**
     * PageUvpController destroy
     * @param Request $request
     * @param Page $pageIdentifier
     * @param PageUvp $uvp
     * @return Response
     */
    public function destroy(Request $request, $pageIdentifier, PageUvp $uvp): Response
    {
        return $this->pageUvpService->destroy($request, $pageIdentifier, $uvp);
    }
}