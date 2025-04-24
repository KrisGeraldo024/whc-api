<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use Illuminate\Support\Facades\Http;
use App\Models\{
    Variation
};
use App\Services\Variation\VariationService;

class VariationController extends Controller
{
    /**
     * @var VariationService
     */
    protected $variationService;

    /**
     * VariationController constructor
     * @param VariationService $variationService
     */
    public function __construct (VariationService $variationService)
    {
        $this->variationService = $variationService;
    }

    /**
     * VariationController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->variationService->index($request);
    }

    /**
     * VariationController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->variationService->store($request);
    }

    /**
     * VariationController show
     * @param  Variation $variation
     * @param  Request $request
     * @return Response
     */
    public function show (Variation $variation,  Request $request): Response
    {
        return $this->variationService->show($variation, $request);
    }

    /**
     * VariationController update
     * @param  Variation $variation
     * @param  Request $request
     * @return Response
     */
    public function update (Variation $variation,  Request $request): Response
    {
        return $this->variationService->update($variation, $request);
    }

    /**
     * VariationController destroy
     * @param  Variation $variation
     * @param  Request $request
     * @return Response
     */
    public function destroy (Variation $variation,  Request $request): Response
    {
        return $this->variationService->destroy($variation, $request);
    }

}
