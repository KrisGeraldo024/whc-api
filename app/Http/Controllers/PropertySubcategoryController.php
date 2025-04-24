<?php

namespace App\Http\Controllers;

use App\Models\PropertySubcategory;
use Illuminate\Http\{
    Request,
    Response
};

use App\Services\Property\PropertySubcategoryService;

class PropertySubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $propertySubcategoryService;

    public function __construct (PropertySubcategoryService $propertySubcategoryService)
    {
        $this->propertySubcategoryService = $propertySubcategoryService;
    }

    public function index (Request $request): Response
    {
        return $this->propertySubcategoryService->index($request);
    }


    public function store (Request $request): Response
    {
        return $this->propertySubcategoryService->store($request);
    }

  
    public function show (PropertySubcategory $property_subcategory, Request $request): Response
    {
        return $this->propertySubcategoryService->show($property_subcategory, $request);
    }

    
    public function update (PropertySubcategory $property_subcategory, Request $request): Response
    {
        return $this->propertySubcategoryService->update($property_subcategory, $request);
    }

    
    public function destroy (PropertySubcategory $property_subcategory, Request $request): Response
    {
        return $this->propertySubcategoryService->destroy($property_subcategory, $request);
    }
}
