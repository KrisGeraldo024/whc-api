<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use Illuminate\Support\Facades\Http;
use App\Models\Discount;
use App\Services\Discount\DiscountService;

class DiscountController extends Controller
{
    /**
     * @var DiscountService
     */
    protected $discountService;

    /**
     * DiscountController constructor
     * @param DiscountService $discountService
     */
    public function __construct (DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    /**
     * DiscountController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->discountService->index($request);
    }

    /**
     * DiscountController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->discountService->store($request);
    }

    /**
     * DiscountController show
     * @param  Discount $discount
     * @param  Request $request
     * @return Response
     */
    public function show (Discount $discount, Request $request): Response
    {
        return $this->discountService->show($discount, $request);
    }

    /**
     * DiscountController update
     * @param  Discount $discount
     * @param  Request $request
     * @return Response
     */
    public function update (Discount $discount, Request $request): Response
    {
        return $this->discountService->update($discount, $request);
    }

    /**
     * DiscountController destroy
     * @param  Discount $discount
     * @param  Request $request
     * @return Response
     */
    public function destroy (Discount $discount, Request $request): Response
    {
        return $this->discountService->destroy($discount, $request);
    }
}
