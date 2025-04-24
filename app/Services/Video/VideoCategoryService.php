<?php

namespace App\Services\Video;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\VideoCategory;
use App\Traits\GlobalTrait;

class VideoCategoryService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * VideoCategoryService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = VideoCategory::orderBy('order')
        ->when($request->filled('all') , function ($query) {
            return $query->get();
        }, function ($query) {
            return $query->paginate(20);
        });

        return response([
            'records' => $records
        ]);
    }

    /**
     * VideoCategoryService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required',
            'order'       => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = VideoCategory::create([
            'title'       => $request->title,
            'order'       => $request->order,
            'slug'        => $this->slugify($request->title, 'VideoCategory')
        ]);

        $this->generateLog($request->user(), "added this video category ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * VideoCategoryService show
     * @param  VideoCategory $category
     * @param  Request $request
     * @return Response
     */
    public function show ($category, $request): Response
    {
        $this->generateLog($request->user(), "viewed this video category ({$category->id}).");
        
        return response([
            'record' => $category
        ]);
    }

    /**
     * VideoCategoryService update
     * @param  VideoCategory $category
     * @param  Request $request
     * @return Response
     */
    public function update ($category, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required',
            'order'       => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $category->update([
            'title'       => $request->title,
            'order'       => $request->order,
            'slug'  => $this->slugify($request->title, 'VideoCategory', $category->id)
        ]);

        $this->generateLog($request->user(), "updated this video category ({$category->id}).");

        return response([
            'record' => $category
        ]);
    }

    /**
     * VideoCategoryService destroy
     * @param  VideoCategory $category
     * @param  Request $request
     * @return Response
     */
    public function destroy ($category, $request): Response
    {
        $category->delete();
        $this->generateLog($request->user(), "deleted this video category ({$category->id}).");

        return response([
            'record' => 'Video category deleted'
        ]);
    }
}
