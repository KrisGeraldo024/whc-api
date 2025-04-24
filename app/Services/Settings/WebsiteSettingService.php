<?php

namespace App\Services\Settings;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\WebsiteSetting;
use App\Traits\GlobalTrait;

class WebsiteSettingService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * FaqService index
     * @param  Request $request
     * @return Response
     */
    
     public function manage($request): Response
    {
        
        $setting = WebsiteSetting::first();

        if (!$setting) {
            $setting = WebsiteSetting::create([
                'address' => $request->address,
                'facebook'  => $request->facebook,
                'instagram' => $request->instagram,
                // 'twitter'   => $request->twitter,
                'youtube'   => $request->youtube,
                // 'tvc'       => $request->tvc,
            ]);
        }
        else {
            $setting->update([
                'address' => $request->address,
                'facebook'  => $request->facebook,
                'instagram' => $request->instagram,
                // 'twitter'   => $request->twitter,
                'youtube'   => $request->youtube,
                // 'tvc'       => $request->tvc,
            ]);
        }

        $this->generateLog($request->user(), "Changed", "Website Settings");

        return response([
            'record' => $setting
        ]);
    }


    /**
     * FaqService show
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function show ($request): Response
    {
        $setting = WebsiteSetting::first();

        if ($setting) {
            // $this->generateLog($request->user(), "viewed this website setting ({$setting->id})");
        }

        return response([
            'record' => $setting
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    
}
