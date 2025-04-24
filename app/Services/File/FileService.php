<?php

namespace App\Services\File;

use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\{
    Validator
};
// use App\Models\File;
use App\Traits\GlobalTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Models\{
    Image,
};
use ZipArchive;

class FileService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * FaqService index
     * @param  Request $request
     * @return Response
     */
    public function download($request): Response
    {
    
       $filePath = $request->input('path');
    //    Log::info('File path received from request: ' . $filePath);

        
        if (!Storage::exists($filePath)) {
            abort(404);
        }

        $file = Storage::get($filePath);

        $response = new Response($file, 200);
        $response->header('Content-Type', 'application/pdf');
        //$response->header('Content-Disposition', 'attachment; filename="' . basename($filePath) . '"');

        return $response;
        
    }

    public function downloadConstructionUpdates($request)
    {
        $path = strstr($request->input('path'), '/uploads');
        $file_extension = pathinfo($path, PATHINFO_EXTENSION);

        $file = public_path().'/storage/'.$path;
        $headers = ['Content-Type' => $this->getContentType($file_extension)];
        $filename = 'output.' . $file_extension;
        
        return response()->download($file, $filename, $headers);         
    }

    // public function downloadConstructionUpdates($request): Response
    // {
    
    //     $imageArray = $request->input('data');
        
    //     if (!Storage::exists($filePath)) {
    //         abort(404);
    //     }

    //     $zip = new ZipArchive();
    //     $zipFileName = 'construction_updates.zip';
    //     $zipFilePath = storage_path('app/' . $zipFileName);


    //     if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    //         abort(500, 'Failed to create zip file.');
    //     }

    //     foreach ($filePaths as $filePath) {
    //         if (!Storage::exists($filePath)) {
    //             continue; // Skip non-existing files
    //         }
    //         $fileContent = Storage::get($filePath);
    //         $fileName = basename($filePath);
    //         $zip->addFromString($fileName, $fileContent);
    //     }
    
    //     $zip->close();


    //     // $file = Storage::get($filePath);

    //     // $response = new Response($file, 200);
    //     // $response->header('Content-Type', 'application/pdf');
    //     // //$response->header('Content-Disposition', 'attachment; filename="' . basename($filePath) . '"');

    //     // return $response;

    //     return response()->download($zipFilePath, $zipFileName, ['Content-Type' => 'application/zip'])->deleteFileAfterSend(true);
        
    // }
}
