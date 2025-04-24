<?php

namespace App\Http\Controllers;

use App\Models\ProjectStatus;
use Illuminate\Http\{
    Request,
    Response
};

use App\Services\Project\ProjectStatusService;

class ProjectStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $projectStatusService;

    public function __construct (ProjectStatusService $projectStatusService)
    {
        $this->projectStatusService = $projectStatusService;
    }

    public function index (Request $request): Response
    {
        return $this->projectStatusService->index($request);
    }


    public function store (Request $request): Response
    {
        return $this->projectStatusService->store($request);
    }

  
    public function show (ProjectStatus $project_status, Request $request): Response
    {
        return $this->projectStatusService->show($project_status, $request);
    }

    
    public function update (ProjectStatus $project_status, Request $request): Response
    {
        return $this->projectStatusService->update($project_status, $request);
    }

    
    public function destroy (ProjectStatus $project_status, Request $request): Response
    {
        return $this->projectStatusService->destroy($project_status, $request);
    }
}
