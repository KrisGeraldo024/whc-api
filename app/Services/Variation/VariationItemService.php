<?php

namespace App\Services\Variation;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Hash,
    Validator
};
use App\Models\{
    Variation,
    VariationItem
};
use App\Traits\GlobalTrait;

class VariationItemService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * VariationItemService index
     * @param  Variation $variation
     * @param  Request $request
     * @return Response
     */
    public function index ($variation, $request): Response
    {
        $records = VariationItem::orderBy('order')
        ->whereVariationId($variation->id)
        ->when($request->filled('type'), function ($query) use ($request) {
            $query->whereType($request->type);
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
     * VariationItemService store
     * @param  Variation $variation
     * @param  Request $request
     * @return Response
     */
    public function store ($variation, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'        => 'required',
            'description'  => 'required',
            'type'         => 'required|in:feature,specification',
            'order'        => 'required|integer',
            'enabled'      => 'required',
            'main_image'   => 'sometimes',
            'main_image.*' => 'sometimes|mimes:jpeg,png,jpg,webp|max:3000'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = VariationItem::create([
            'variation_id'  => $variation->id,
            'title'       => $request->title,
            'description' => $request->description,
            'type'        => $request->type,
            'order'       => $request->order,
            'enabled'     => $request->enabled
        ]);

        $this->generateLog($request->user(), "added this variation items ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * VariationItemService show
     * @param  Variation $variation
     * @param  VariationItem $item
     * @param  Request $request
     * @return Response
     */
    public function show ($variation, $item, $request): Response
    {
        $this->generateLog($request->user(), "viewed this variation items ({$item->id}).");

        return response([
            'record' => $item
        ]);
    }

    /**
     * VariationItemService update
     * @param  Variation $variation
     * @param  VariationItem $item
     * @param  Request $request
     * @return Response
     */
    public function update ($variation, $item, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'        => 'required',
            'description'  => 'required',
            'type'         => 'required|in:feature,specification',
            'order'        => 'required|integer',
            'enabled'      => 'required',
            'main_image'   => 'sometimes',
            'main_image.*' => 'sometimes|mimes:jpeg,png,jpg,webp|max:3000'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $item->update([
            'title'       => $request->title,
            'description' => $request->description,
            'type'        => $request->type,
            'order'       => $request->order,
            'enabled'     => $request->enabled
        ]);

        $this->generateLog($request->user(), "update this variation items ({$item->id}).");

        return response([
            'record' => $item
        ]);
    }

    /**
     * VariationItemService destroy
     * @param  Variation $variation
     * @param  VariationItem $item
     * @param  Request $request
     * @return Response
     */
    public function destroy ($variation, $item, $request): Response
    {
        $this->generateLog($request->user(), "deleted this variation items ({$item->id}).");

        $item->delete();

        return response([
            'record' => 'Variation item deleted successfully!'
        ]);
    }
}
