<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\Stat;
use App\Services\Stat\StatService;

class StatController extends Controller
{
    /**
     * @var StatService
     */
    protected $statService;

    /**
     * StatController constructor
     * @param StatService $statService
     */
    public function __construct (StatService $statService)
    {
        $this->statService = $statService;
    }

    /**
     * StatController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->statService->index($request);
    }

    /**
     * StatController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->statService->store($request);
    }

    /**
     * StatController show
     * @param  Stat $stat
     * @param  Request $request
     * @return Response
     */
    public function show (Stat $stat, Request $request): Response
    {
        return $this->statService->show($stat, $request);
    }

    /**
     * StatController update
     * @param  Stat $stat
     * @param  Request $request
     * @return Response
     */
    public function update (Stat $stat, Request $request): Response
    {
        return $this->statService->update($stat, $request);
    }

    /**
     * StatController destroy
     * @param  Stat $stat
     * @param  Request $request
     * @return Response
     */
    public function destroy (Stat $stat, Request $request): Response
    {
        return $this->statService->destroy($stat, $request);
    }
}
