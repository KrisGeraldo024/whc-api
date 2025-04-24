<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\{
    Request,
    Response
};
use App\Http\Requests\DepartmentRequest;
use App\Services\Department\DepartmentService;

class DepartmentController extends Controller
{
    protected $departmentService;

    public function __construct (DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    public function index (Request $request): Response
    {
        return $this->departmentService->index($request);
    }

    public function store (DepartmentRequest $request): Response
    {
        return $this->departmentService->store($request);
    }
  
    public function show (Department $department, Request $request): Response
    {
        return $this->departmentService->show($department, $request);
    }
    
    public function update (Department $department, DepartmentRequest $request): Response
    {
        return $this->departmentService->update($department, $request);
    }
    
    public function destroy (Department $department, Request $request): Response
    {
        return $this->departmentService->destroy($department, $request);
    }
}
