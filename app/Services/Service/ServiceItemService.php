<?php

namespace App\Services\Service;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\{
    Service,
    ServiceItem
};
use App\Traits\GlobalTrait;

class ServiceItemService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * ServiceItemService index
     * @param  Service $service
     * @param  Request $request
     * @return Response
     */
    public function index ($service, $request): Response
    {
        $records = ServiceItem::orderBy('order')
        ->whereServiceId($service->id)
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
     * ServiceItemService store
     * @param  Service $service
     * @param  Request $request
     * @return Response
     */
    public function store ($service, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'        => 'required',
            'description'  => 'required',
            'type'         => 'required|in:service,process,faq',
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

        $record = ServiceItem::create([
            'service_id'  => $service->id,
            'title'       => $request->title,
            'description' => $request->description,
            'type'        => $request->type,
            'order'       => $request->order,
            'enabled'     => $request->enabled
        ]);

        if ($request->type == 'process') {
            if ($request->hasFile('main_image')) {
                $this->addImages('service_item', $request, $record, 'main_image');
            }
        }

        $this->generateLog($request->user(), "added this service item ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * ServiceItemService show
     * @param  Service $service
     * @param  ServiceItem $item
     * @param  Request $request
     * @return Response
     */
    public function show ($service, $item, $request): Response
    {
        $item->load('service', 'images');
        $this->generateLog($request->user(), "viewed this service item ({$item->id}).");
        
        return response([
            'record' => $item
        ]);
    }

    /**
     * ServiceItemService update
     * @param  Service $service
     * @param  ServiceItem $item
     * @param  Request $request
     * @return Response
     */
    public function update ($service, $item, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'        => 'required',
            'description'  => 'required',
            'type'         => 'required|in:service,process,faq',
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
            'service_id'  => $service->id,
            'title'       => $request->title,
            'description' => $request->description,
            'type'        => $request->type,
            'order'       => $request->order,
            'enabled'     => $request->enabled
        ]);

        if ($request->type == 'process') {
            $this->updateImages('service_item', $request, $item, 'main_image');
        }

        $this->generateLog($request->user(), "updated this service item ({$item->id}).");

        return response([
            'record' => $item
        ]);
    }

    /**
     * ServiceItemService destroy
     * @param  Service $service
     * @param  ServiceItem $item
     * @param  Request $request
     * @return Response
     */
    public function destroy ($service, $item, $request): Response
    {
        $item->delete();
        $this->generateLog($request->user(), "deleted this service item ({$item->id}).");

        return response([
            'record' => 'Service item deleted'
        ]);
    }
}
