<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class PdfController extends Controller
{
    public function generatePdf(Request $request)
    {
        // Retrieve array data from the request
        $imageArray = $request->input('data');

        // Load the view and pass the data
        $pdf = PDF::loadView('pdf.template', ['imageArray' => $imageArray]);
        //$pdf = Pdf::loadView('pdf.template');
        // Set the filename for the generated PDF
        //$filename = 'generated_pdf.pdf';

        // Save the PDF to the storage path if needed
        // $pdf->save(storage_path('app/public/pdfs/' . $filename));

        // Return the PDF as a response
        return $pdf->stream('output.pdf');
    }
}
