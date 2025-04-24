<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use Illuminate\Http\{
    Request,
    Response
};

use App\Services\Property\AmenityService;
use App\Http\Requests\AmenityRequest;

class AmenityController extends Controller
{
    protected $amenityService;

    public function __construct (AmenityService $amenityService)
    {
        $this->amenityService = $amenityService;
    }

    public function index (Request $request): Response
    {
        return $this->amenityService->index($request);
    }

    public function store (Request $request): Response
    {
        return $this->amenityService->store($request);
    }
  
    public function show (Amenity $amenity, Request $request): Response
    {
        return $this->amenityService->show($amenity, $request);
    }
    
    public function update (Amenity $amenity, Request $request): Response
    {
        return $this->amenityService->update($amenity, $request);
    }

    public function destroy (Amenity $amenity, Request $request): Response
    {
        return $this->amenityService->destroy($amenity, $request);
    }
}
