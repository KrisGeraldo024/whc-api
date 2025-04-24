<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use Illuminate\Support\Facades\Http;
use App\Models\{
    Variation,
    VariationItem
};
use App\Services\Variation\VariationItemService;

class VariationItemController extends Controller
{
    /**
     * @var VariationItemService
     */
    protected $variationItemService;

    /**
     * VariationItemController constructor
     * @param VariationItemService $variationItemService
     */
    public function __construct (VariationItemService $variationItemService)
    {
        $this->variationItemService = $variationItemService;
    }

    /**
     * VariationItemController index
     * @param  Variation $variation
     * @param  Request $request
     * @return Response
     */
    public function index (Variation $variation, Request $request): Response
    {
        return $this->variationItemService->index($variation, $request);
    }

    /**
     * VariationItemController store
     * @param  Variation $variation
     * @param  Request $request
     * @return Response
     */
    public function store (Variation $variation, Request $request): Response
    {
        return $this->variationItemService->store($variation, $request);
    }

    /**
     * VariationItemController show
     * @param  Variation $variation
     * @param  VariationItem $item
     * @param  Request $request
     * @return Response
     */
    public function show (Variation $variation, VariationItem $item, Request $request): Response
    {
        return $this->variationItemService->show($variation, $item, $request);
    }

    /**
     * VariationItemController update
     * @param  Variation $variation
     * @param  VariationItem $item
     * @param  Request $request
     * @return Response
     */
    public function update (Variation $variation, VariationItem $item, Request $request): Response
    {
        return $this->variationItemService->update($variation, $item, $request);
    }

    /**
     * VariationItemController destroy
     * @param  Variation $variation
     * @param  VariationItem $item
     * @param  Request $request
     * @return Response
     */
    public function destroy (Variation $variation, VariationItem $item, Request $request): Response
    {
        return $this->variationItemService->destroy($variation, $item, $request);
    }
}
