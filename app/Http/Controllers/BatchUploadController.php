<?php

namespace App\Http\Controllers;

use App\Models\BatchUpload;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\{
    Services\BatchUpload\BatchUploadService
};

class BatchUploadController extends Controller
{

    protected $batchUploadService;

    public function __construct(BatchUploadService $batchUploadService)
    {
      $this->batchUploadService = $batchUploadService;
    }
   
    public function store(Request $request)
    {
        return $this->batchUploadService->store($request);
    }

}