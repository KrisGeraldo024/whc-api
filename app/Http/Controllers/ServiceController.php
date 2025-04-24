<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\Service;
use App\Services\Service\ServiceService;

class ServiceController extends Controller
{
    /**
     * @var ServiceService
     */
    protected $serviceService;

    /**
     * ServiceController constructor
     * @param ServiceService $serviceService
     */
    public function __construct (ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    /**
     * ServiceController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->serviceService->index($request);
    }

    /**
     * ServiceController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->serviceService->store($request);
    }

    /**
     * ServiceController show
     * @param  Service $service
     * @param  Request $request
     * @return Response
     */
    public function show (Service $service, Request $request): Response
    {
        return $this->serviceService->show($service, $request);
    }

    /**
     * ServiceController update
     * @param  Service $service
     * @param  Request $request
     * @return Response
     */
    public function update (Service $service, Request $request): Response
    {
        return $this->serviceService->update($service, $request);
    }

    /**
     * ServiceController destroy
     * @param  Service $service
     * @param  Request $request
     * @return Response
     */
    public function destroy (Service $service, Request $request): Response
    {
        return $this->serviceService->destroy($service, $request);
    }
}
