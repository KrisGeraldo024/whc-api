<?php

namespace App\Http\Controllers;

use App\Models\Biller;
use Illuminate\Http\{
    Request,
    Response
};
use App\Http\Requests\BillerRequest;
use App\Services\Biller\BillerService;

class BillerController extends Controller
{
    protected $billerService;

    public function __construct (BillerService $billerService)
    {
        $this->billerService = $billerService;
    }

    public function index (Request $request): Response
    {
        return $this->billerService->index($request);
    }

    public function store (BillerRequest $request): Response
    {
        return $this->billerService->store($request);
    }
  
    public function show (Biller $biller, Request $request): Response
    {
        return $this->billerService->show($biller, $request);
    }
    
    public function update (Biller $biller, BillerRequest $request): Response
    {
        return $this->billerService->update($biller, $request);
    }
    
    public function destroy (Biller $biller, Request $request): Response
    {
        return $this->billerService->destroy($biller, $request);
    }
}
