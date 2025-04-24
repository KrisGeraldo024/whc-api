<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\{
    Request,
    Response
};

use App\Services\File\FileService;

class FileController extends Controller
{

    protected $fileService;

    public function __construct (FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function download(Request $request): Response
    {
        return $this->fileService->download($request);
    }

    public function downloadConstructionUpdates(Request $request):  \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return $this->fileService->downloadConstructionUpdates($request);
    }


    // public function downloadFile(Request $request)
    // {
    //     // Retrieve array data from the request
    //     $imageArray = $request->input('data');

    //     // Load the view and pass the data
    //     $pdf = PDF::loadView('pdf.template', ['imageArray' => $imageArray]);
    //     //$pdf = Pdf::loadView('pdf.template');
    //     // Set the filename for the generated PDF
    //     //$filename = 'generated_pdf.pdf';

    //     // Save the PDF to the storage path if needed
    //     // $pdf->save(storage_path('app/public/pdfs/' . $filename));

    //     // Return the PDF as a response
    //     return $pdf->stream('output.pdf');
    // }

    
}
