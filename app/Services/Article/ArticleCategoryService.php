<?php

namespace App\Services\Article;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\ArticleCategory;
use App\Traits\GlobalTrait;

class ArticleCategoryService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * ArticleCategoryService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = ArticleCategory::orderBy('order')
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
     * ArticleCategoryService store
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

        $record = ArticleCategory::create([
            'title'       => $request->title,
            'order'       => $request->order,
            'slug'        => $this->slugify($request->title, 'ArticleCategory')
        ]);

        $this->generateLog($request->user(), "added this article category ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * ArticleCategoryService show
     * @param  ArticleCategory $category
     * @param  Request $request
     * @return Response
     */
    public function show ($category, $request): Response
    {
        $this->generateLog($request->user(), "viewed this article category ({$category->id}).");
        
        return response([
            'record' => $category
        ]);
    }

    /**
     * ArticleCategoryService update
     * @param  ArticleCategory $category
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
            'slug'  => $this->slugify($request->title, 'ArticleCategory', $category->id)
        ]);

        $this->generateLog($request->user(), "updated this article category ({$category->id}).");

        return response([
            'record' => $category
        ]);
    }

    /**
     * ArticleCategoryService destroy
     * @param  ArticleCategory $category
     * @param  Request $request
     * @return Response
     */
    public function destroy ($category, $request): Response
    {
        $category->delete();
        $this->generateLog($request->user(), "deleted this article category ({$category->id}).");

        return response([
            'record' => 'Article category deleted'
        ]);
    }
}
