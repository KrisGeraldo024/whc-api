<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\{
    ServiceItem,
    Service
};
use App\Services\Service\ServiceItemService;

class ServiceItemController extends Controller
{
    /**
     * @var ServiceItemService
     */
    protected $serviceItemService;

    /**
     * ServiceItemController constructor
     * @param ServiceItemService $serviceItemService
     */
    public function __construct (ServiceItemService $serviceItemService)
    {
        $this->serviceItemService = $serviceItemService;
    }

    /**
     * ServiceItemController index
     * @param  Service $service
     * @param  Request $request
     * @return Response
     */
    public function index (Service $service, Request $request): Response
    {
        return $this->serviceItemService->index($service, $request);
    }

    /**
     * ServiceItemController store
     * @param  Service $service
     * @param  Request $request
     * @return Response
     */
    public function store (Service $service, Request $request): Response
    {
        return $this->serviceItemService->store($service, $request);
    }

    /**
     * ServiceItemController show
     * @param  Service $service
     * @param  ServiceItem $item
     * @param  Request $request
     * @return Response
     */
    public function show (Service $service, ServiceItem $item, Request $request): Response
    {
        return $this->serviceItemService->show($service, $item, $request);
    }

    /**
     * ServiceItemController update
     * @param  Service $service
     * @param  ServiceItem $item
     * @param  Request $request
     * @return Response
     */
    public function update (Service $service, ServiceItem $item, Request $request): Response
    {
        return $this->serviceItemService->update($service, $item, $request);
    }

    /**
     * ServiceItemController destroy
     * @param  Service $service
     * @param  ServiceItem $item
     * @param  Request $request
     * @return Response
     */
    public function destroy (Service $service, ServiceItem $item, Request $request): Response
    {
        return $this->serviceItemService->destroy($service, $item, $request);
    }
}
