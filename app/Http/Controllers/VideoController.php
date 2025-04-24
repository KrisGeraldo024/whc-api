<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\Video;
use App\Services\Video\VideoService;

class VideoController extends Controller
{
    /**
     * @var VideoService
     */
    protected $videoService;

    /**
     * VideoController constructor
     * @param VideoService $videoService
     */
    public function __construct (VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * VideoController index
     * @param  Request  $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->videoService->index($request);
    }

    /**
     * VideoController store
     * @param  Request  $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->videoService->store($request);
    }

    /**
     * VideoController show
     * @param  Video $video
     * @return Response
     */
    public function show (Video $video, Request $request): Response
    {
        return $this->videoService->show($video, $request);
    }

    /**
     * VideoController update
     * @param  Video $video
     * @param  Request $request
     * @return Response
     */
    public function update (Video $video, Request $request): Response
    {
        return $this->videoService->update($video, $request);
    }

    /**
     * VideoController destroy
     * @param  Video $video
     * @param  Request $request
     * @return Response
     */
    public function destroy (Video $video, Request $request): Response
    {
        return $this->videoService->destroy($video, $request);
    }
}
