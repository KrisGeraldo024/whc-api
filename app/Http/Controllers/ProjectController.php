<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\{
    Request,
    Response
};

use App\Services\Project\ProjectService;
use App\Http\Requests\ProjectRequest;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $projectService;

    public function __construct (ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index (Request $request): Response
    {
        return $this->projectService->index($request);
    }


    public function store (Request $request): Response
    {
        return $this->projectService->store($request);
    }

  
    public function show (Project $project, Request $request): Response
    {
        return $this->projectService->show($project, $request);
    }

    
    public function update (Project $project, Request $request): Response
    {
        return $this->projectService->update($project, $request);
    }

    
    public function destroy (Project $project, Request $request): Response
    {
        return $this->projectService->destroy($project, $request);
    }
}
