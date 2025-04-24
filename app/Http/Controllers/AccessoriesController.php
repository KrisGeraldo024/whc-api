<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use Illuminate\Support\Facades\Http;
use App\Models\Accessories;
use App\Services\Accessories\AccessoriesService;

class AccessoriesController extends Controller
{
    /**
     * @var AccessoriesService
     */
    protected $accessoriesService;

    /**
     * AccessoriesController constructor
     * @param AccessoriesService $accessoriesService
     */
    public function __construct (AccessoriesService $accessoriesService)
    {
        $this->accessoriesService = $accessoriesService;
    }

    /**
     * AccessoriesController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->accessoriesService->index($request);
    }

    /**
     * AccessoriesController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->accessoriesService->store($request);
    }

    /**
     * AccessoriesController show
     * @param  Accessories $accessory
     * @param  Request $request
     * @return Response
     */
    public function show (Accessories $accessory, Request $request): Response
    {
        return $this->accessoriesService->show($accessory, $request);
    }

    /**
     * AccessoriesController update
     * @param  Accessories $accessory
     * @param  Request $request
     * @return Response
     */
    public function update (Accessories $accessory, Request $request): Response
    {
        return $this->accessoriesService->update($accessory, $request);
    }

    /**
     * AccessoriesController destroy
     * @param  Accessories $accessory
     * @param  Request $request
     * @return Response
     */
    public function destroy (Accessories $accessory, Request $request): Response
    {
        return $this->accessoriesService->destroy($accessory, $request);
    }
}
