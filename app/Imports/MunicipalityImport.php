<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\{ 
	Province,
    Municipality
};
use App\Traits\GlobalTrait;

class MunicipalityImport implements ToCollection
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
					$province = $row[1];
					$name = $row[2];

					$municipalityExisted = false;

					$municipality = Municipality::where('code', $code)->first();

					if (!$municipality) {
                        $provinceData = Province::where('code', $province)->first();
                        if ($provinceData) {
                            Municipality::create([
                                'province_id' => $provinceData->id,
                                'code' => $code,
                                'name' => $name,
                            ]);
                        }
					}   

					if ($municipalityExisted) {
                        $this->success("Row " . ($key + 1) . ": Municipality Updated", $key);
					} else {
						$this->success("Row " . ($key + 1) . ": Municipality Added", $key);
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
