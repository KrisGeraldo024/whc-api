<?php

namespace App\Services\Import;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator,
    Storage,
};
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\{
    ProvinceImport,
    MunicipalityImport,
    BarangayImport,
};

use App\Traits\GlobalTrait;

class ImportService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
    * ImportService upload
    * @param Request $request
    * @return Response
    */
    public function upload($request): Response
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ], 403);
        }

        $import = (object) [
            'results' => []
        ];

        switch ($request->type) {
            case 'province':
                $import = new ProvinceImport;
                Excel::import($import, $request->file);
                break;
            case 'municipality':
                $import = new MunicipalityImport;
                Excel::import($import, $request->file);
                break;
            case 'barangay':
                $import = new BarangayImport;
                Excel::import($import, $request->file);
                break;
        }

        $this->generateLog($request->user(), "import this module ({$request->type})");

        return response([
            'records' => $import->results
        ]);
    }
}