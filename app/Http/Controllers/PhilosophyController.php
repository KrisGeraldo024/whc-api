<?php

namespace App\Http\Controllers;

use App\Models\Philosophy;
use Illuminate\Http\{
    Request,
    Response
};

use App\Http\Requests\PhilosophyRequest;
use App\Services\Philosophy\PhilosophyService;

class PhilosophyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $philosophyService;

    public function __construct (PhilosophyService $philosophyService)
    {
        $this->philosophyService = $philosophyService;
    }

    public function index (Request $request): Response
    {
        return $this->philosophyService->index($request);
    }

    public function store (PhilosophyRequest $request): Response
    {
        return $this->philosophyService->store($request);
    }
  
    public function show (Philosophy $philosophy, Request $request): Response
    {
        return $this->philosophyService->show($philosophy, $request);
    }
    
    public function update (Philosophy $philosophy, PhilosophyRequest $request): Response
    {
        return $this->philosophyService->update($philosophy, $request);
    }
    
    public function destroy (Philosophy $philosophy, Request $request): Response
    {
        return $this->philosophyService->destroy($philosophy, $request);
    }
}