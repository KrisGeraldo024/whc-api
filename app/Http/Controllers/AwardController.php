<?php

namespace App\Http\Controllers;

use App\Models\Award;
use Illuminate\Http\{
    Request,
    Response
};
use App\Http\Requests\AwardsRequest;
use App\Services\Award\AwardService;

class AwardController extends Controller
{
    protected $awardService;

    public function __construct (AwardService $awardService)
    {
        $this->awardService = $awardService;
    }

    public function index (Request $request): Response
    {
        return $this->awardService->index($request);
    }

    public function store (AwardsRequest $request): Response
    {
        return $this->awardService->store($request);
    }
  
    public function show (Award $award, Request $request): Response
    {
        return $this->awardService->show($award, $request);
    }
    
    public function update (Award $award, AwardsRequest $request): Response
    {
        return $this->awardService->update($award, $request);
    }
    
    public function destroy (Award $award, Request $request): Response
    {
        return $this->awardService->destroy($award, $request);
    }

    public function getAwards (Request $request): Response
    {
        return $this->awardService->getAwards($request);
    }
}