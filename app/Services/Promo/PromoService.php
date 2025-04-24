<?php

namespace App\Services\Promo;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Promo;
use App\Traits\GlobalTrait;

class PromoService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * PromoService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = Promo::orderBy('start_date')
        ->with(['branch', 'formDetails'])
        ->when($request->filled('branch'), function ($query) use ($request) {
            $query->whereBranchId($request->branch);
        })
        ->when($request->filled('all'), function ($query) {
            return $query->get();
        }, function ($query) {
            return $query->paginate(20);
        });

        return response([
            'records' => $records
        ]);
    }

    /**
     * PromoService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'branch_id'      => 'sometimes',
            'title'          => 'required',
            'summary'        => 'required',
            'description'    => 'required',
            'start_date'     => 'required',
            'end_date'       => 'required',
            'featured'       => 'required',
            'enabled'        => 'required',
            'teaser_image'   => 'required',
            'main_image'     => 'required',
            'teaser_image.*' => 'required|mimes:jpeg,png,jpg,webp|max:3000',
            'main_image.*'   => 'required|mimes:jpeg,png,jpg,webp|max:3000',
            'button_label'   => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $this->handleSettingFeaturedPromo($request);

        $record = Promo::create([
            'branch_id'   => $request->branch_id,
            'title'       => $request->title,
            'summary'     => $request->summary,
            'description' => $request->description,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'featured'    => $request->featured,
            'enabled'     => $request->enabled,
            'slug'        => $this->slugify($request->title, 'Promo'),
            'button_label'=> $request->button_label
        ]);

        if ($request->hasFile('teaser_image')) {
            $this->addImages('promo', $request, $record, 'teaser_image');
        }
        if ($request->hasFile('main_image')) {
            $this->addImages('promo', $request, $record, 'main_image');
        }

        $this->metatags($record, $request);
        $this->generateLog($request->user(), "added this promo ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * PromoService show
     * @param  Promo $promo
     * @param  Request $request
     * @return Response
     */
    public function show ($promo, $request): Response
    {
        $promo->load('images', 'metadata');
        $this->generateLog($request->user(), "viewed this promo ({$promo->id}).");

        return response([
            'record' => $promo
        ]);
    }

    /**
     * PromoService update
     * @param  Promo $promo
     * @param  Request $request
     * @return Response
     */
    public function update ($promo, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'branch_id'      => 'sometimes',
            'title'          => 'required',
            'summary'        => 'required',
            'description'    => 'required',
            'start_date'     => 'required',
            'end_date'       => 'required',
            'featured'       => 'required',
            'enabled'        => 'required',
            'teaser_image'   => 'sometimes',
            'main_image'     => 'sometimes',
            'teaser_image.*' => 'sometimes|mimes:jpeg,png,jpg,webp|max:3000',
            'main_image.*'   => 'sometimes|mimes:jpeg,png,jpg,webp|max:3000',
            'button_label'   => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        if($promo->featured === 0){
            $this->handleSettingFeaturedPromo($request);
        }

        $promo->update([
            'branch_id'   => $request->branch_id,
            'title'       => $request->title,
            'summary'     => $request->summary,
            'description' => $request->description,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'featured'    => $request->featured,
            'enabled'     => $request->enabled,
            'slug'        => $this->slugify($request->title, 'Promo', $promo->id),
            'button_label'=> $request->button_label
        ]);

        $this->updateImages('promo', $request, $promo, 'teaser_image');
        $this->updateImages('promo', $request, $promo, 'main_image');

        $this->metatags($promo, $request);
        $this->generateLog($request->user(), "updated this promo ({$promo->id}).");

        return response([
            'record' => $promo
        ]);
    }

    /**
     * PromoService destroy
     * @param  Promo $promo
     * @param  Request $request
     * @return Response
     */
    public function destroy ($promo, $request): Response
    {
        $promo->delete();
        $this->generateLog($request->user(), "deleted this promo ({$promo->id}).");

        return response([
            'record' => 'Promo deleted'
        ]);
    }

    /**
     * PromoService store
     * @param  Request $request
     */
    public function handleSettingFeaturedPromo ($request) {
        if ($request->featured == 1) {
            //check if there are promos
            $promos = Promo::select('id', 'featured')
                ->get();

            if (count($promos) > 0) {
                foreach($promos as $key => $promo) {
                    $promo->update([
                        'featured' => 0
                    ]);
                }
            }
        }
    }
}
