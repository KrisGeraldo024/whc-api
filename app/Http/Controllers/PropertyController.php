<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\{
    Request,
    Response
};

use App\Http\Requests\PropertyRequest;
use App\Services\Property\PropertyService;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $propertyService;

    public function __construct (PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    public function index (Request $request): Response
    {
        return $this->propertyService->index($request);
    }

    public function store (PropertyRequest $request): Response
    {
        return $this->propertyService->store($request);
    }
  
    public function show (Property $property, Request $request): Response
    {
        return $this->propertyService->show($property, $request);
    }
    
    public function update (Property $property, PropertyRequest $request): Response
    {
        return $this->propertyService->update($property, $request);
    }
    
    public function destroy (Property $property, Request $request): Response
    {
        return $this->propertyService->destroy($property, $request);
    }
    
    public function getProperty (Request $request): Response
    {
        return $this->propertyService->getProperty( $request);
    }
    
    public function getRelateds (Request $request, $property, $related): Response
    {
        return $this->propertyService->getRelateds( $request, $property, $related);
    }
    
    public function getAll (Request $request): Response
    {
        return $this->propertyService->getAll($request);
    }

    public function getLocationsByType (Request $request): Response
    {
        return $this->propertyService->getLocationsByType($request);
    }

    public function getPropertyList (Request $request): Response
    {
        return $this->propertyService->getPropertyList();
    }

    public function search (Request $request): Response
    {
        return $this->propertyService->search($request);
    }

    public function getSuggesteds (Request $request): Response
    {
        return $this->propertyService->getSuggesteds($request);
    }
}
