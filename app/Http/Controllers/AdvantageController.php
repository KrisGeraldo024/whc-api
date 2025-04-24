<?php

namespace App\Http\Controllers;

use App\Models\Advantage;
use Illuminate\Http\{
    Request,
    Response
};

use App\Http\Requests\AdvantageRequest;
use App\Services\Advantage\AdvantageService;


class AdvantageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $advantageService;

    public function __construct (AdvantageService $advantageService)
    {
        $this->advantageService = $advantageService;
    }

    public function index (Request $request): Response
    {
        return $this->advantageService->index($request);
    }

    public function store (AdvantageRequest $request): Response
    {
        return $this->advantageService->store($request);
    }
  
    public function show (Advantage $advantage, Request $request): Response
    {
        return $this->advantageService->show($advantage, $request);
    }
    
    public function update (Advantage $advantage, AdvantageRequest $request): Response
    {
        return $this->advantageService->update($advantage, $request);
    }
    
    public function destroy (Advantage $advantage, Request $request): Response
    {
        return $this->advantageService->destroy($advantage, $request);
    }
}