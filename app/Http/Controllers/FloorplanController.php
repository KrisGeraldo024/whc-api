<?php

namespace App\Http\Controllers;

use App\Models\Floorplan;
use Illuminate\Http\{
    Request,
    Response
};

use App\Services\Project\FloorplanService;
use App\Http\Requests\FloorplanRequest;

class FloorplanController extends Controller
{
    protected $floorplanService;

    public function __construct (FloorplanService $floorplanService)
    {
        $this->floorplanService = $floorplanService;
    }

    public function index (Request $request): Response
    {
        return $this->floorplanService->index($request);
    }

    public function store (Request $request): Response
    {
        return $this->floorplanService->store($request);
    }
  
    public function show (Floorplan $floorplan, Request $request): Response
    {
        return $this->floorplanService->show($floorplan, $request);
    }
    
    public function update (Floorplan $floorplan, Request $request): Response
    {
        return $this->floorplanService->update($floorplan, $request);
    }

    public function destroy (Floorplan $floorplan, Request $request): Response
    {
        return $this->floorplanService->destroy($floorplan, $request);
    }
}