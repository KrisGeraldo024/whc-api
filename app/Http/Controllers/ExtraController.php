<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\{
    Image
};
use App\Services\Extra\ExtraService;

class ExtraController extends Controller
{
    /**
     * @var ExtraService
     */
    protected $extraService;

    /**
     * ExtraController constructor
     * @param ExtraService $ExtraService
     */
    public function __construct (ExtraService $extraService)
    {
        $this->extraService = $extraService;
    }

    /**
     * ExtraController dashboard
     * @param  Request  $request
     * @return Response
     */
    public function dashboard (): Response
    {
        return $this->extraService->dashboard();
    }

    /**
     * ExtraService captchaVerification
     * @param  Request $request
     * @return Object
     */
    public function captchaVerification (Request $request): Object
    {
        return $this->extraService->captchaVerification($request);
    }

    /**
     * ExtraController deleteImage
     * @param  Image $image
     * @param  Request $request
     * @return Response
     */
    public function deleteImage (Image $image, Request $request): Response
    {
        return $this->extraService->deleteImage($image, $request);
    }

    /**
     * ExtraController copyAllImages
     * @param  Request $request
     * @param  string $type
     */
    public function copyAllImages (Request $request, string $type)
    {
        return $this->extraService->copyAllImages($request, $type);
    }

    /**
     * ExtraController copyAllImages
     * @param  Request $request
     * @param  string $type
     */
    public function deleteItem (Request $request)
    {
        return $this->extraService->deleteItem($request);
    }

    public function reOrder(Request $request): Response
    {
        return $this->extraService->reOrder($request);
    }
}