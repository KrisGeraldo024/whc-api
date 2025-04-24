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
    PageTab
};
use App\Services\Page\PageTabServices;

class PageTabController extends Controller
{
   /**
     * @var PageTabServices
     */
    protected $pageTabServices;

    /**
     * PageTabController constructor
     * @param PageTabServices $PageTabServices
     */
    public function __construct (PageTabServices $pageTabServices)
    {

        $this->pageTabServices = $pageTabServices;
    }

    /**
     * PageTabController index
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function index(Request $request, $pageIdentifier): Response
    {

        // return response([
        //     'records' => $request->all()
        // ], 403);
        return $this->pageTabServices->index($request, $pageIdentifier);
    }


    /**
     * PageBannerController store
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function store(Request $request, $pageIdentifier): Response
    {
        return $this->pageTabServices->store($request, $pageIdentifier);
    }

    /**
     * PageBannerController show
     * @param Request $request
     * @param Page $page
     * @param PageTab $tab
     * @return Response
     */
    public function show(Request $request, $pageIdentifier, PageTab $tab): Response
    {
        return $this->pageTabServices->show($request, $pageIdentifier, $tab);
    }

    /**
     * PageBannerController update
     * @param Request $request
     * @param Page $page
     * @param PageTab $tab
     * @return Response
     */
    public function update(Request $request, $pageIdentifier, PageTab $tab): Response
    {
        return $this->pageTabServices->update($request, $pageIdentifier, $tab);
    }

    /**
     * PageBannerController destroy
     * @param Request $request
     * @param Page $page
     * @param PageTab $tab
     * @return Response
     */
    public function destroy(Request $request, $pageIdentifier, PageTab $tab): Response
    {
        return $this->pageTabServices->destroy($request, $pageIdentifier, $tab);
    }
}
