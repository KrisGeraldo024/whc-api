<?php

namespace App\Http\Controllers;


use Illuminate\Http\{
    Request,
    Response
};

use App\Models\WebsiteSetting;

use App\Services\Settings\WebsiteSettingService;

class WebsiteSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     protected $websiteSettingService;

    public function __construct (WebsiteSettingService $websiteSettingService)
    {
        $this->websiteSettingService = $websiteSettingService;
    }

   
    public function manage(Request $request): Response
    {
        return $this->websiteSettingService->manage($request);
    }

    public function show (Request $request): Response
    {
        return $this->websiteSettingService->show($request);
    }

  
}
