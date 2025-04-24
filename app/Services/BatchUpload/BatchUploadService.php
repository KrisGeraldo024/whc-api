<?php

namespace App\Services\BatchUpload;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\GlobalTrait;
use App\Models\{
    BatchUpload
};
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ArticleImport;
use App\Imports\PropertyImport;
use App\Imports\UnitsImport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


class BatchUploadService
{
    use GlobalTrait;


    public function store(Request $request): Response
    {
        $batchUpload = BatchUpload::create();
    
        if ($request->hasFile('file')) {
            $this->addImages('batch_upload', $request, $batchUpload, 'file');
        }
    
        $batchUpload->load('images');
    
        $fileUrl = $batchUpload->images[0]->path; // Assuming $file is the URL
    
        // Download the file content
        $response = Http::get($fileUrl);
        $tempFilePath = storage_path('app/'.Str::random(20).'.xlsx'); // Adjust the path as needed
        // Save the file content to a temporary file
        file_put_contents($tempFilePath, $response->body());
        
        try {
            if ($request->query('query') === "articles") {
                Excel::import(new ArticleImport, $tempFilePath);
                $this->generateLog($request->user(), "Created", "News & Articles");
            } 
    
            if ($request->query('query') === "properties") {
                Excel::import(new PropertyImport, $tempFilePath);
                $this->generateLog($request->user(), "Created", "Properties");
            }
    
            if ($request->query('query') === "units") {
                Excel::import(new UnitsImport, $tempFilePath);
                $this->generateLog($request->user(), "Created", "Units");
            }
    
            // Delete the temporary file
            unlink($tempFilePath);
    
            return response([
                'records' => "Success"
            ], 200);
        } catch (\Exception $e) {
            // Delete the temporary file on error as well
            unlink($tempFilePath);
    
            return response([
                'error' => 'Import failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}