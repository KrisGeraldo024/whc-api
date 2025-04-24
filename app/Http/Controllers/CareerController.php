<?php

namespace App\Http\Controllers;

use App\Models\Career;
use Illuminate\Http\{
    Request,
    Response
};
use App\Http\Requests\CareerRequest;
use App\Services\Career\CareerService;

class CareerController extends Controller
{
    protected $careerService;

    public function __construct (CareerService $careerService)
    {
        $this->careerService = $careerService;
    }

    public function index (Request $request): Response
    {
        return $this->careerService->index($request);
    }

    public function store (CareerRequest $request): Response
    {
        return $this->careerService->store($request);
    }
  
    public function show (Career $career, Request $request): Response
    {
        return $this->careerService->show($career, $request);
    }
    
    public function update (Career $career, CareerRequest $request): Response
    {
        return $this->careerService->update($career, $request);
    }
    
    public function destroy (Career $career, Request $request): Response
    {
        return $this->careerService->destroy($career, $request);
    }
    
    public function getCareers ( Request $request): Response
    {
        return $this->careerService->getCareers( $request);
    }
    
    public function getCareer ( Request $request): Response
    {
        return $this->careerService->getCareer( $request);
    }
}
