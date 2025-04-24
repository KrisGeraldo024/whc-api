<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\{
    Request,
    Response
};

use App\Http\Requests\OfficeRequest;
use App\Services\Office\OfficeService;

class OfficeController extends Controller
{
    protected $officeService;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct (OfficeService $officeService)
    {
        $this->officeService = $officeService;
    }

    public function index (Request $request): Response
    {
        return $this->officeService->index($request);
    }


    public function store (OfficeRequest $request): Response
    {
        return $this->officeService->store($request);
    }


    public function show (Office $office, Request $request): Response
    {
        return $this->officeService->show($office, $request);
    }

    public function update (Office $office, OfficeRequest $request): Response
    {
        return $this->officeService->update($office, $request);
    }


    public function destroy (Office $office, Request $request): Response
    {
        return $this->officeService->destroy($office, $request);
    }
}
