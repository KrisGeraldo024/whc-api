<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Services\Subscribe\SubscribeService;

class SubscribeController extends Controller
{
    /**
     * @var SubscribeService
     */
    protected $SubscribeService;

    /**
     * SubscribeController constructor
     * @param SubscribeService $subscribeService
     */
    public function __construct (SubscribeService $subscribeService)
    {
        $this->subscribeService = $subscribeService;
    }

    /**
     * SubscribeController index
     * @param  Request  $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->subscribeService->index($request);
    }

    /**
     * SubscribeController subscribe
     * @param  Request  $request
     * @return Response
     */
    public function subscribe (Request $request): Response
    {
        return $this->subscribeService->subscribe($request);
    }

    /**
     * SubscribeController unsubscribe
     * @param  Request  $request
     * @return Response
     */
    public function unsubscribe (Request $request): Response
    {
        return $this->subscribeService->unsubscribe($request);
    }
}
