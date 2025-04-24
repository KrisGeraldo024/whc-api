<?php

namespace App\Services\Service;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\ServiceCategory;
use App\Traits\GlobalTrait;

class ServiceCategoryService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * ServiceCategoryService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = ServiceCategory::orderBy('order')
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
     * ServiceCategoryService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'order' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = ServiceCategory::create([
            'title' => $request->title,
            'order' => $request->order,
            'slug'  => $this->slugify($request->title, 'ServiceCategory')
        ]);

        $this->generateLog($request->user(), "added this service category ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * ServiceCategoryService show
     * @param  ServiceCategory $category
     * @param  Request $request
     * @return Response
     */
    public function show ($category, $request): Response
    {
        $this->generateLog($request->user(), "viewed this service category ({$category->id}).");
        
        return response([
            'record' => $category
        ]);
    }

    /**
     * ServiceCategoryService update
     * @param  ServiceCategory $category
     * @param  Request $request
     * @return Response
     */
    public function update ($category, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'order' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $category->update([
            'title' => $request->title,
            'order' => $request->order,
            'slug'  => $this->slugify($request->title, 'ServiceCategory', $category->id)
        ]);

        $this->generateLog($request->user(), "updated this service category ({$category->id}).");

        return response([
            'record' => $category
        ]);
    }

    /**
     * ServiceCategoryService destroy
     * @param  ServiceCategory $category
     * @param  Request $request
     * @return Response
     */
    public function destroy ($category, $request): Response
    {
        $category->delete();
        $this->generateLog($request->user(), "deleted this service category ({$category->id}).");

        return response([
            'record' => 'Service category deleted'
        ]);
    }
}
