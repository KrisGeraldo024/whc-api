<?php

namespace App\Imports;

use Illuminate\Support\Str;

use Carbon\Carbon;
use App\Models\Article;
use App\Models\Taxonomy;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ArticleImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
      //skip first row
      $collection = $collection->slice(1);
      $categories = Taxonomy::where('type', Taxonomy::TYPE_ARTICLE_CATEGORY)->get();

      try  {
        foreach ($collection as $row) 
        {
          if (!isset($row[0], $row[1], $row[2], $row[3])) {
              continue; // Skip rows with missing data
          }
            //get category name and ID
            $category = $categories->firstWhere('name', $row[0] ?? 'News');
            if (!$category) {
                continue; // Skip if category not found
            }
            //generate keywords
            $keywords = sprintf(
                '%s,%s,%s,%s,%s,%s',
                $row[1],
                Str::slug($row[1]),
                Str::slug($row[1], '_'),
                $row[0],
                Str::slug($row[0]),
                Str::slug($row[0], '_')
              );

              // $carbonDate = Carbon::parse($row[3]);
              if (isset($row[3])) {
                $excelSerialDate = (int)$row[3];
                $carbonDate = Carbon::createFromDate(1900, 1, 1)->addDays($excelSerialDate - 2); // Subtract 2 days: one for the base date and one for Excel's leap year bug
              } else {
                $carbonDate = Carbon::now();
              }

            $record = Article::create([
                'category_id'   =>  $category->id,
                'title'         =>  $row[1],
                'slug'          =>  Str::slug($row[1]),
                'keyword'      =>  $keywords,
                'content'       =>  $row[2],
                'date'          =>  $carbonDate->format('Y-m-d H:i:s'),
                'enabled'       =>   1,
                'featured'      => 0,
                'order' => Article::count() + 1
            ]);
        }
      } catch (\Exception $e) 
      {
          \Log::info($e);
      }
    }
}