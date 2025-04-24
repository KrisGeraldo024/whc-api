<?php

namespace App\Http\Controllers;

use App\Models\Architect;
use Illuminate\Http\{
    Request,
    Response
};

use App\Services\Project\ArchitectService;


class ArchitectController extends Controller
{
    protected $architectService;

    public function __construct (ArchitectService $architectService)
    {
        $this->architectService = $architectService;
    }

    public function index (Request $request): Response
    {
        return $this->architectService->index($request);
    }

    public function store (Request $request): Response
    {
        return $this->architectService->store($request);
    }
  
    public function show (Architect $architect, Request $request): Response
    {
        return $this->architectService->show($architect, $request);
    }
    
    public function update (Architect $architect, Request $request): Response
    {
        return $this->architectService->update($architect, $request);
    }

    public function destroy (Architect $architect, Request $request): Response
    {
        return $this->architectService->destroy($architect, $request);
    }
}

