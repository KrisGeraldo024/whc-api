<?php

namespace App\Http\Controllers;

use App\Models\BusinessUnit;
use Illuminate\Http\{
    Request,
    Response
};
use App\Services\BusinessUnit\BusinessUnitService;

class BusinessUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $businessUnitService;

    public function __construct (BusinessUnitService $businessUnitService)
    {
        $this->businessUnitService = $businessUnitService;
    }

    public function index (Request $request): Response
    {
        return $this->businessUnitService->index($request);
    }


    public function store (Request $request): Response
    {
        return $this->businessUnitService->store($request);
    }

  
    public function show (BusinessUnit $business_unit, Request $request): Response
    {
        return $this->businessUnitService->show($business_unit, $request);
    }

    
    public function update (BusinessUnit $business_unit, Request $request): Response
    {
        return $this->businessUnitService->update($business_unit, $request);
    }

    
    public function destroy (BusinessUnit $business_unit, Request $request): Response
    {
        return $this->businessUnitService->destroy($business_unit, $request);
    }
}
