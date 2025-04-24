<?php

namespace App\Services\HearingAid;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\HearingAidCategory;
use App\Traits\GlobalTrait;

class HearingAidCategoryService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * HearingAidCategoryService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = HearingAidCategory::orderBy('order')
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
     * HearingAidCategoryService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required',
            'color_theme' => 'required',
            'order'       => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = HearingAidCategory::create([
            'title'       => $request->title,
            'color_theme' => $request->color_theme,
            'order'       => $request->order,
            'slug'        => $this->slugify($request->title, 'HearingAidCategory')
        ]);

        $this->generateLog($request->user(), "added this hearing aid category ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * HearingAidCategoryService show
     * @param  HearingAidCategory $category
     * @param  Request $request
     * @return Response
     */
    public function show ($category, $request): Response
    {
        $this->generateLog($request->user(), "viewed this hearing aid category ({$category->id}).");
        
        return response([
            'record' => $category
        ]);
    }

    /**
     * HearingAidCategoryService update
     * @param  HearingAidCategory $category
     * @param  Request $request
     * @return Response
     */
    public function update ($category, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required',
            'color_theme' => 'required',
            'order'       => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $category->update([
            'title'       => $request->title,
            'color_theme' => $request->color_theme,
            'order'       => $request->order,
            'slug'  => $this->slugify($request->title, 'HearingAidCategory', $category->id)
        ]);

        $this->generateLog($request->user(), "updated this hearing aid category ({$category->id}).");

        return response([
            'record' => $category
        ]);
    }

    /**
     * HearingAidCategoryService destroy
     * @param  HearingAidCategory $category
     * @param  Request $request
     * @return Response
     */
    public function destroy ($category, $request): Response
    {
        $category->delete();
        $this->generateLog($request->user(), "deleted this hearing aid category ({$category->id}).");

        return response([
            'record' => 'Hearing aid category deleted'
        ]);
    }
}
