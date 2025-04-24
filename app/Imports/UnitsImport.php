<?php

namespace App\Imports;

use Illuminate\Support\Str;

use Carbon\Carbon;
use App\Models\Property;
use App\Models\Taxonomy;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UnitsImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
      //skip first row
      $collection = $collection->slice(1);
      $properties = Property::get(['id', 'name', 'gmaps_link']);
      $units = Taxonomy::where('type', Taxonomy::TYPE_UNIT)->get(['id', 'name']);
      // \Log::info($locations);
      try  {
        foreach ($collection as $row) 
        {
          // if (!isset($row[0], $row[1], $row[2], $row[3])) {
          //     continue; // Skip rows with missing data
          // }
            //get category name and ID
            $property = $properties->filter(function ($property) use ($row) {
                return stripos($property->name, $row[0]) !== false; // Case-insensitive search
            })->first();
            
            \Log::info($property);
            // if (!$location) {
            //     continue; // Skip if category not found
            // }
            $unit = $units->filter(function ($unit) use ($row) {
              return stripos($unit->name, $row[5]) !== false; // Case-insensitive search
            })->first();
            // if (!$status) {
            //     continue; // Skip if category not found
            // }


            $record = Unit::create([
                'parent_id'       => $property->id,
                'location'        => $row[1],
                'name'            => $row[2] ?? $unit->name,
                'slug'            =>  Str::slug($row[2] ?? $unit->name),
                'gmap_url'        => $property->gmaps_link ?? '',
                'starts_at'       => $row[4] ?? '',
                'unit_type'       => $unit->id,
                'floor_area'      => $row[6] ?? 0,
                'lot_area'        => $row[7] ?? 0,
                'bedroom'         => $row[8] ?? 0,
                't_and_b'         => $row[9] ?? 0,
                'storeys'         => $row[10] ?? 0,
                'powder_room'     => $row[11] ?? 0,
                'enabled'         => 1,
                'order'           => Unit::where('parent_id', $property->id)->count() + 1
            ]);
            \Log::info($record);
        }
      } catch (\Exception $e) {
          \Log::info($e);
      }
    }
}