<?php

namespace App\Http\Controllers;

use App\Models\ProjectAward;
use Illuminate\Http\{
    Request,
    Response
};

use App\Http\Requests\ProjectAwardRequest;
use App\Services\Project\ProjectAwardService;

class ProjectAwardController extends Controller
{
    protected $projectAwardService;
    
    public function __construct (ProjectAwardService $projectAwardService)
    {
        $this->projectAwardService = $projectAwardService;
    }

    public function index (Request $request): Response
    {
        return $this->projectAwardService->index($request);
    }

   
    public function store (ProjectAwardRequest $request): Response
    {
        return $this->projectAwardService->store($request);
    }

    
    public function show (ProjectAward $project_award, Request $request): Response
    {
        return $this->projectAwardService->show($project_award, $request);
    }

    
    public function update (ProjectAward $project_award, ProjectAwardRequest $request): Response
    {
        return $this->projectAwardService->update($project_award, $request);
    }

    
    public function destroy (ProjectAward $project_award, Request $request): Response
    {
        return $this->projectAwardService->destroy($project_award, $request);
    }
}
