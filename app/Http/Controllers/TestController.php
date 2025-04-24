<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class TestController extends Controller
{
    public function test ()
    {

        return 'asdasdas';

        // $now = Carbon::now();
        // $tables = ['Inquiry', 'AdsInquiry'];
        
        // for ($i = 0; $i < count($tables); $i++) { 
        //     $model = '\App\Models\\' . $tables[$i];
        //     $rows = $model::all();

        //     foreach ($rows as $key => $row) {
        //         $date = Carbon::parse($row->created_at)->format('Y-m-d');
        //         $diff = $now->diffInDays($date, false);
        //         if ($diff <= -90) {
        //             $row->delete();
        //         }
        //     }
        // }
        

        // $response = json_decode(Http::get('https://v6.exchangerate-api.com/v6/dad20580843fd1c8692b7d9c/latest/PHP')->body());
        // foreach ($response->conversion_rates as $key => $value) {
        //     CurrencyRate::updateOrCreate([
        //     'name' => $key,
        //     ],[
        //     'amount' => $value
        //     ]);
        // }

        // return $response;
    }
}
