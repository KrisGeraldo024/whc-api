<?php

namespace App\Services\HearingAid;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\{
    HearingAid,
    HearingAidItem
};
use App\Traits\GlobalTrait;

class HearingAidItemService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * HearingAidItemService index
     * @param  HearingAid $hearing_aid
     * @param  Request $request
     * @return Response
     */
    public function index ($hearing_aid, $request): Response
    {
        $records = HearingAidItem::orderBy('order')
        ->whereHearingAidId($hearing_aid->id)
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
     * HearingAidItemService store
     * @param  HearingAid $hearing_aid
     * @param  Request $request
     * @return Response
     */
    public function store ($hearing_aid, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'             => 'required',
            'description'       => 'sometimes',
            'type'              => 'required|in:feature,uvp,specification',
            'order'             => 'required|integer',
            'enabled'           => 'required',
            'main_image'        => 'required',
            'thumbnail_image'   => 'sometimes',
            'main_image.*'      => 'required|mimes:jpeg,png,jpg,webp|max:3000',
            'thumbnail_image.*' => 'sometimes|mimes:jpeg,png,jpg,webp|max:3000'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = HearingAidItem::create([
            'hearing_aid_id' => $hearing_aid->id,
            'title'          => $request->title,
            'description'    => $request->description,
            'type'           => $request->type,
            'order'          => $request->order,
            'enabled'        => $request->enabled
        ]);

        if ($request->hasFile('main_image')) {
            $this->addImages('hearing_aid_item', $request, $record, 'main_image');
        }

        if ($request->type == 'uvp') {
            if ($request->hasFile('thumbnail_image')) {
                $this->addImages('hearing_aid_item', $request, $record, 'thumbnail_image');
            }
        }

        $this->generateLog($request->user(), "added this hearing aid item ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * HearingAidItemService show
     * @param  HearingAid $hearing_aid
     * @param  ServiceItem $item
     * @param  Request $request
     * @return Response
     */
    public function show ($hearing_aid, $item, $request): Response
    {
        $item->load('hearingAid', 'images');
        $this->generateLog($request->user(), "viewed this hearing aid item ({$item->id}).");
        
        return response([
            'record' => $item
        ]);
    }

    /**
     * HearingAidItemService update
     * @param  HearingAid $hearing_aid
     * @param  HearingAidItem $item
     * @param  Request $request
     * @return Response
     */
    public function update ($hearing_aid, $item, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'             => 'required',
            'description'       => 'sometimes',
            'type'              => 'required|in:feature,uvp,specification',
            'order'             => 'required|integer',
            'enabled'           => 'required',
            'main_image'        => 'sometimes',
            'thumbnail_image'   => 'sometimes',
            'main_image.*'      => 'required|mimes:jpeg,png,jpg,webp|max:3000',
            'thumbnail_image.*' => 'sometimes|mimes:jpeg,png,jpg,webp|max:3000'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $item->update([
            'hearing_aid_id' => $hearing_aid->id,
            'title'          => $request->title,
            'description'    => $request->description,
            'type'           => $request->type,
            'order'          => $request->order,
            'enabled'        => $request->enabled
        ]);

        $this->updateImages('hearing_aid_item', $request, $item, 'main_image');
        if ($request->type == 'uvp') {
            if ($request->has('thumbnail_image_alt')) {
                $this->updateImages('hearing_aid_item', $request, $item, 'thumbnail_image');
            }
        }

        $this->generateLog($request->user(), "updated this hearing aid item ({$item->id}).");

        return response([
            'record' => $item
        ]);
    }

    /**
     * HearingAidItemService destroy
     * @param  HearingAid $hearing_aid
     * @param  HearingAidItem $item
     * @param  Request $request
     * @return Response
     */
    public function destroy ($hearing_aid, $item, $request): Response
    {
        $item->delete();
        $this->generateLog($request->user(), "deleted this hearing aid item ({$item->id}).");

        return response([
            'record' => 'Hearing aid item deleted'
        ]);
    }
}
