<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\{ 
    Municipality,
    Barangay
};
use App\Traits\GlobalTrait;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BarangayImport implements ToCollection
{
	public $results = [];
	public $success = [];
	public $errors = [];
	public $totalRows = 0;
	public $errorsCount = 0;
	public $successCount = 0;

	/**
	 * @var GlobalTrait
	 */
	use GlobalTrait;

	/**
	* @param Collection $collection
	*/
	public function collection(Collection $collection)
	{
		foreach($collection as $key =>  $row) {
			if ($key > 0) { # skip the first row kasi puro title lang nandun
				if ($row->filter()->isNotEmpty()) {
					# increment totalRows
					$this->totalRows++;

					# store the rows in understandable variable names
					$code = $row[0];
					$municipality = $row[1];
					$name = $row[2];

					$barangayExisted = false;

					$barangay = Barangay::where('code', $code)->first();

					if (!$barangay) {
							$municipalityData = Municipality::where('code', $municipality)->first();
							if ($municipalityData) {
                                Barangay::create([
                                        'municipality_id' => $municipalityData->id,
                                        'code' => $code,
                                        'name' => $name,
                                ]);
							}
					}   

					if ($barangayExisted) {
                        $this->success("Row " . ($key + 1) . ": Barangay Updated", $key);
					} else {
						$this->success("Row " . ($key + 1) . ": Barangay Added", $key);
					}
				}
			}
		}
	}

	protected function success ($message, $key) {
		$this->successCount++;
		array_push($this->success, $message);
		array_push($this->results, [
			'message' => $message,
			'type' => 'success'
		]);
	}

	protected function error ($message, $key) {
		$this->errorsCount++;
		array_push($this->errors, $message);
		array_push($this->results, [
			'message' => $message,
			'type' => 'error'
		]);
	}
}
