<?php

namespace App\Http\Controllers;

use App\Models\Vicinity;
use Illuminate\Http\{
    Request,
    Response
};

use App\Services\Project\VicinityService;
use App\Http\Requests\VicinityRequest;

class VicinityController extends Controller
{
    protected $vicinityService;

    public function __construct (VicinityService $vicinityService)
    {
        $this->vicinityService = $vicinityService;
    }

    public function index (Request $request): Response
    {
        return $this->vicinityService->index($request);
    }


    public function store (Request $request): Response
    {
        return $this->vicinityService->store($request);
    }

  
    public function show (Vicinity $vicinity, Request $request): Response
    {
        return $this->vicinityService->show($vicinity, $request);
    }

    
    public function update (Vicinity $vicinity, Request $request): Response
    {
        return $this->vicinityService->update($vicinity, $request);
    }

    
    public function destroy (Vicinity $vicinity, Request $request): Response
    {
        return $this->vicinityService->destroy($vicinity, $request);
    }
}
