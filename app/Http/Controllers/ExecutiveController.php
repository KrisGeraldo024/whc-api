<?php

namespace App\Http\Controllers;

use App\Models\Executive;
use Illuminate\Http\{
    Request,
    Response
};

use App\Http\Requests\ExecutiveRequest;
use App\Services\Leader\ExecutiveService;

class ExecutiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $executiveService;

    public function __construct (ExecutiveService $executiveService)
    {
        $this->executiveService = $executiveService;
    }

    public function index (Request $request): Response
    {
        return $this->executiveService->index($request);
    }

    public function store (ExecutiveRequest $request): Response
    {
        return $this->executiveService->store($request);
    }
  
    public function show (Executive $executive, Request $request): Response
    {
        return $this->executiveService->show($executive, $request);
    }
    
    public function update (Executive $executive, ExecutiveRequest $request): Response
    {
        return $this->executiveService->update($executive, $request);
    }
    
    public function destroy (Executive $executive, Request $request): Response
    {
        return $this->executiveService->destroy($executive, $request);
    }
}