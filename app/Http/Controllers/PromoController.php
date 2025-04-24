<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\Promo;
use App\Services\Promo\PromoService;

class PromoController extends Controller
{
    /**
     * @var PromoService
     */
    protected $promoService;

    /**
     * PromoController constructor
     * @param PromoService $promoService
     */
    public function __construct (PromoService $promoService)
    {
        $this->promoService = $promoService;
    }

    /**
     * PromoController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->promoService->index($request);
    }

    /**
     * PromoController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->promoService->store($request);
    }

    /**
     * PromoController show
     * @param  Promo $promo
     * @param  Request $request
     * @return Response
     */
    public function show (Promo $promo, Request $request): Response
    {
        return $this->promoService->show($promo, $request);
    }

    /**
     * PromoController update
     * @param  Promo $promo
     * @param  Request $request
     * @return Response
     */
    public function update (Promo $promo, Request $request): Response
    {
        return $this->promoService->update($promo, $request);
    }

    /**
     * PromoController destroy
     * @param  Promo $promo
     * @param  Request $request
     * @return Response
     */
    public function destroy (Promo $promo, Request $request): Response
    {
        return $this->promoService->destroy($promo, $request);
    }
}
