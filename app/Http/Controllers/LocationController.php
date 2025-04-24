<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\{
    Request,
    Response
};
use App\Services\Location\LocationService;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $locationService;

    public function __construct (LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function index (Request $request): Response
    {
        return $this->locationService->index($request);
    }


    public function store (Request $request): Response
    {
        return $this->locationService->store($request);
    }

  
    public function show (Location $location, Request $request): Response
    {
        return $this->locationService->show($location, $request);
    }

    
    public function update (Location $location, Request $request): Response
    {
        return $this->locationService->update($location, $request);
    }

    
    public function destroy (Location $location, Request $request): Response
    {
        return $this->locationService->destroy($location, $request);
    }
}
