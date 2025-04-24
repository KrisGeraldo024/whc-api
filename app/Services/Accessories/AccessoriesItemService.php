<?php

namespace App\Services\Accessories;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Hash,
    Validator
};
use App\Models\{
    Accessories,
    AccessoriesItem,
};
use App\Traits\GlobalTrait;

class AccessoriesItemService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * AccessoriesItemService index
     * @param  Accessories $accessory
     * @param  Request $request
     * @return Response
     */
    public function index ($accessory, $request): Response
    {
        $records = AccessoriesItem::orderBy('order')
        ->whereAccessoriesId($accessory->id)
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
     * AccessoriesItemService store
     * @param  Accessories $accessory
     * @param  Request $request
     * @return Response
     */
    public function store ($accessory, $request): Response
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

        $record = AccessoriesItem::create([
            'accessories_id'  => $accessory->id,
            'title'       => $request->title,
            'description' => $request->description,
            'type'        => $request->type,
            'order'       => $request->order,
            'enabled'     => $request->enabled
        ]);

        if ($request->type == 'feature') {
            if ($request->hasFile('main_image')) {
                $this->addImages('accessories_item', $request, $record, 'main_image');
            }
        }

        $this->generateLog($request->user(), "added this accessories items ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * AccessoriesItemService show
     * @param  Accessories $accessory
     * @param  AccessoriesItem $item
     * @param  Request $request
     * @return Response
     */
    public function show ($accessory, $item, $request): Response
    {
        $this->generateLog($request->user(), "viewed this accessories items ({$item->id}).");

        $item->load('images');

        return response([
            'record' => $item
        ]);
    }

    /**
     * AccessoriesItemService update
     * @param  Accessories $accessory
     * @param  AccessoriesItem $item
     * @param  Request $request
     * @return Response
     */
    public function update ($accessory, $item, $request): Response
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

        if ($request->type == 'feature') {
            $this->updateImages('accessories_item', $request, $item, 'main_image');
        }
        else {
            $item->images()->delete();
        }

        $this->generateLog($request->user(), "update this accessories items ({$item->id}).");

        $item->load('images');

        return response([
            'record' => $item
        ]);
    }

    /**
     * AccessoriesItemService destroy
     * @param  Accessories $accessory
     * @param  AccessoriesItem $item
     * @param  Request $request
     * @return Response
     */
    public function destroy ($accessory, $item, $request): Response
    {
        $this->generateLog($request->user(), "deleted this accessories items ({$item->id}).");

        $item->delete();

        return response([
            'record' => 'Accessories item deleted successfully!'
        ]);
    }

}
