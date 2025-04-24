<?php

namespace App\Imports;

use Illuminate\Support\Str;

use Carbon\Carbon;
use App\Models\Property;
use App\Models\Taxonomy;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PropertyImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
      //skip first row
      $collection = $collection->slice(1);
      $locations = Taxonomy::where('type', Taxonomy::TYPE_PROPERTY_LOCATION)->get(['id', 'name']);
      $statuses = Taxonomy::where('type', Taxonomy::TYPE_PROPERTY_STATUS)->get(['id', 'name']);
      // \Log::info($locations);
      try  {
        foreach ($collection as $row) 
        {
          // if (!isset($row[0], $row[1], $row[2], $row[3])) {
          //     continue; // Skip rows with missing data
          // }
            //get category name and ID
            $location = $locations->firstWhere('name', $row[1]);
            
            // \Log::info($locations->get());
            // if (!$location) {
            //     continue; // Skip if category not found
            // }
            $status = $statuses->firstWhere('name', $row[17]);
            // if (!$status) {
            //     continue; // Skip if category not found
            // }


            $record = Property::create([
                'location_id'     => $location->id,
                'property_type'   => $row[0],
                'name'            => $row[2],
                'slug'            =>  Str::slug($row[2]),
                'gmaps_link'      => $row[3] ?? '',
                'property_size'   => $row[5] ?? '',
                'starts_at'       => $row[6] ?? '',
                'address'         => $row[7],
                'description'     => $row[8],
                'status_id'       => $status->id,
                'enabled'         => 1,
                'featured'        => 0,
                'order'           => Property::where('property_type', $row[0])->count() + 1,
                'towers'          => $row[18] === '-' ? 0 : $row[18]
            ]);
            \Log::info($record);
        }
      } catch (\Exception $e) {
          \Log::info($e);
      }
    }
}