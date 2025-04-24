<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\{
    Request,
    Response
};

use App\Http\Requests\StoryRequest;
use App\Services\Story\StoryService;

class StoryController extends Controller
{

    protected $storyService;

    public function __construct (StoryService $storyService)
    {
        $this->storyService = $storyService;
    }

    public function index (Request $request): Response
    {
        return $this->storyService->index($request);
    }

    public function store (StoryRequest $request): Response
    {
        return $this->storyService->store($request);
    }
  
    public function show (Story $story, Request $request): Response
    {
        return $this->storyService->show($story, $request);
    }
    
    public function update (Story $story, StoryRequest $request): Response
    {
        return $this->storyService->update($story, $request);
    }
    
    public function destroy (Story $story, Request $request): Response
    {
        return $this->storyService->destroy($story, $request);
    }

}
