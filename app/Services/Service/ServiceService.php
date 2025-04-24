<?php

namespace App\Services\Service;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Service;
use App\Traits\GlobalTrait;

class ServiceService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * ServiceService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = Service::orderBy('order')
        ->with('serviceCategory')
        ->when($request->filled('category'), function ($query) use ($request) {
            $query->whereServiceCategoryId($request->category);
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
     * ServiceService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'service_category_id' => 'required',
            'title'               => 'required',
            'subtitle'            => 'required',
            'summary'             => 'required',
            'description'         => 'sometimes',
            'order'               => 'required|integer',
            'enabled'             => 'required',
            'featured'            => 'required',
            'icon'                => 'required',
            'main_image'          => 'required',
            'gallery_field'       => 'sometimes',
            'icon.*'              => 'required|mimes:svg,png,webp|max:3000',
            'main_image.*'        => 'required|mimes:jpeg,png,jpg,webp|max:3000',
            'gallery_field.*'     => 'sometimes|mimes:jpeg,png,jpg,webp'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = Service::create([
            'service_category_id' => $request->service_category_id,
            'title'               => $request->title,
            'subtitle'            => $request->subtitle,
            'summary'             => $request->summary,
            'gallery_title'       => $request->gallery_section_title,
            'order'               => $request->order,
            'enabled'             => $request->enabled,
            'featured'            => $request->featured,
            'description'         => $request->description,
            'slug'                => $this->slugify($request->title, 'Service')
        ]);

        if ($request->hasFile('icon')) {
            $this->addImages('service', $request, $record, 'icon');
        }
        if ($request->hasFile('main_image')) {
            $this->addImages('service', $request, $record, 'main_image');
        }
        if ($request->hasFile('gallery')) {
            $this->addImages('service', $request, $record, 'gallery');
        }

        $this->metatags($record, $request);
        $this->generateLog($request->user(), "added this service ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * ServiceService show
     * @param  Service $service
     * @param  Request $request
     * @return Response
     */
    public function show ($service, $request): Response
    {
        $service->load('images', 'metadata');
        $this->generateLog($request->user(), "viewed this service ({$service->id}).");

        return response([
            'record' => $service
        ]);
    }

    /**
     * ServiceService update
     * @param  Service $service
     * @param  Request $request
     * @return Response
     */
    public function update ($service, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'service_category_id' => 'required',
            'title'               => 'required',
            'subtitle'            => 'required',
            'summary'             => 'required',
            'description'         => 'sometimes',
            'order'               => 'required|integer',
            'enabled'             => 'required',
            'featured'            => 'required',
            'icon'                => 'sometimes',
            'main_image'          => 'sometimes',
            'gallery_field'       => 'sometimes',
            'icon.*'              => 'sometimes|mimes:svg,png,webp|max:3000',
            'main_image.*'        => 'sometimes|mimes:jpeg,png,jpg,webp|max:3000',
            'gallery_field.*'      => 'sometimes|mimes:jpeg,png,jpg,webp'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }
        
        $service->update([
            'service_category_id' => $request->service_category_id,
            'title'               => $request->title,
            'subtitle'            => $request->subtitle,
            'summary'             => $request->summary,
            'description'         => $request->description,
            'gallery_title'       => $request->gallery_section_title,
            'order'               => $request->order,
            'enabled'             => $request->enabled,
            'featured'            => $request->featured,
            'slug'                => $this->slugify($request->title, 'Service', $service->id)
        ]);        

        $this->updateImages('service', $request, $service, 'icon');
        $this->updateImages('service', $request, $service, 'main_image');

        if ($request->has('gallery_alt')) {
            $this->updateImages('service', $request, $service, 'gallery');
        }

        $this->metatags($service, $request);
        $this->generateLog($request->user(), "updated this service ({$service->id}).");

        return response([
            'record' => $service
        ]);
    }

    /**
     * ServiceService destroy
     * @param  Service $service
     * @param  Request $request
     * @return Response
     */
    public function destroy ($service, $request): Response
    {
        $service->delete();
        $this->generateLog($request->user(), "deleted this service ({$service->id}).");

        return response([
            'record' => 'Service deleted'
        ]);
    }
}
