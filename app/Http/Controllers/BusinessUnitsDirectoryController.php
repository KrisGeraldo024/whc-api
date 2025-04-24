<?php

namespace App\Http\Controllers;

use App\Models\BusinessUnitsDirectory;
use App\Services\BusinessUnit\BusinessUnitsDirectoryService;
use Illuminate\Http\{
    Request,
    Response
};

class BusinessUnitsDirectoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $businessUnitDirectoryService;

    public function __construct (BusinessUnitsDirectoryService $businessUnitDirectoryService)
    {
        $this->businessUnitDirectoryService = $businessUnitDirectoryService;
    }

    public function index (Request $request): Response
    {
        return $this->businessUnitDirectoryService->index($request);
    }


    public function store (Request $request): Response
    {
        return $this->businessUnitDirectoryService->store($request);
    }

  
    public function show (BusinessUnitsDirectory $business_units_directory, Request $request): Response
    {
        return $this->businessUnitDirectoryService->show($business_units_directory, $request);
    }

    
    public function update (BusinessUnitsDirectory $business_units_directory, Request $request): Response
    {
        return $this->businessUnitDirectoryService->update($business_units_directory, $request);
    }

    
    public function destroy (BusinessUnitsDirectory $business_units_directory, Request $request): Response
    {
        return $this->businessUnitDirectoryService->destroy($business_units_directory, $request);
    }
}
