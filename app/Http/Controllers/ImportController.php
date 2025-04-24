<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Services\Import\ImportService;

class ImportController extends Controller
{
    /**
     * @var ImportService
     */
    protected $importService;

    /**
     * ImportController constructor
     * @param ImportService $importService
     */
    public function __construct (ImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * ImportController upload
     * @param Request $request
     * @return Response
     */
    public function upload(Request $request): Response
    {
        return $this->importService->upload($request);
    }
}
