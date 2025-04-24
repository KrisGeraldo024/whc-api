<?php

namespace App\Http\Controllers;

use App\Models\Landmark;
use Illuminate\Http\{
    Request,
    Response
};

use App\Services\Property\LandmarkService;

class LandmarkController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $LandmarkService;

    public function __construct (LandmarkService $LandmarkService)
    {
        $this->LandmarkService = $LandmarkService;
    }

    public function index (Request $request): Response
    {
        return $this->LandmarkService->index($request);
    }

    public function store (Request $request): Response
    {
        return $this->LandmarkService->store($request);
    }
  
    public function show (Landmark $landmark, Request $request): Response
    {
        return $this->LandmarkService->show($landmark, $request);
    }
    
    public function update (Landmark $landmark, Request $request): Response
    {
        return $this->LandmarkService->update($landmark, $request);
    }
    
    public function destroy (Landmark $landmark, Request $request): Response
    {
        return $this->LandmarkService->destroy($landmark, $request);
    }
}