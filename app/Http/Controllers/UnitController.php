<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\{
    Request,
    Response
};

use App\Services\Property\UnitService;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $UnitService;

    public function __construct (UnitService $UnitService)
    {
        $this->UnitService = $UnitService;
    }

    public function index (Request $request): Response
    {
        return $this->UnitService->index($request);
    }

    public function store (Request $request): Response
    {
        return $this->UnitService->store($request);
    }
  
    public function show (Unit $Unit, Request $request): Response
    {
        return $this->UnitService->show($Unit, $request);
    }
    
    public function update (Unit $Unit, Request $request): Response
    {
        return $this->UnitService->update($Unit, $request);
    }
    
    public function destroy (Unit $Unit, Request $request): Response
    {
        return $this->UnitService->destroy($Unit, $request);
    }
        
    public function getUnit (Request $request): Response
    {
        return $this->UnitService->getUnit( $request);
    }
}