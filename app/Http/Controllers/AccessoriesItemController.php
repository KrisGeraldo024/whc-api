<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use Illuminate\Support\Facades\Http;
use App\Models\{
    Accessories,
    AccessoriesItem,
};
use App\Services\Accessories\AccessoriesItemService;

class AccessoriesItemController extends Controller
{
    /**
     * @var AccessoriesItemService
     */
    protected $accessoriesItemService;

    /**
     * AccessoriesItemController constructor
     * @param AccessoriesItemService $accessoriesItemService
     */
    public function __construct (AccessoriesItemService $accessoriesItemService)
    {
        $this->accessoriesItemService = $accessoriesItemService;
    }

    /**
     * AccessoriesItemController index
     * @param  Accessories $accessory
     * @param  Request $request
     * @return Response
     */
    public function index (Accessories $accessory, Request $request): Response
    {
        return $this->accessoriesItemService->index($accessory, $request);
    }

    /**
     * AccessoriesItemController store
     * @param  Accessories $accessory
     * @param  Request $request
     * @return Response
     */
    public function store (Accessories $accessory, Request $request): Response
    {
        return $this->accessoriesItemService->store($accessory, $request);
    }

    /**
     * AccessoriesItemController show
     * @param  Accessories $accessory
     * @param  Accessoriesitem $item
     * @param  Request $request
     * @return Response
     */
    public function show (Accessories $accessory, Accessoriesitem $item, Request $request): Response
    {
        return $this->accessoriesItemService->show($accessory, $item, $request);
    }

    /**
     * AccessoriesItemController update
     * @param  Accessories $accessory
     * @param  Accessoriesitem $item
     * @param  Request $request
     * @return Response
     */
    public function update (Accessories $accessory, Accessoriesitem $item, Request $request): Response
    {
        return $this->accessoriesItemService->update($accessory, $item, $request);
    }

    /**
     * AccessoriesItemController destroy
     * @param  Accessories $accessory
     * @param  Accessoriesitem $item
     * @param  Request $request
     * @return Response
     */
    public function destroy (Accessories $accessory, Accessoriesitem $item, Request $request): Response
    {
        return $this->accessoriesItemService->destroy($accessory, $item, $request);
    }
}
