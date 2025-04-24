<?php

namespace App\Services\Page;

use Illuminate\Http\{
  Request,
  Response
};
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\{
    Validator,
    Facade,
};
use App\Models\{
    Page,
    PageTag
};
use App\Traits\GlobalTrait;


class PageTagService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * PageTagService index
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function index($request, $identifier): Response
    {
        $page = Page::whereIdentifier($identifier)->first();
        $tags = PageTag::orderBy('sequence')
        ->where('page_id', $page->id)
        ->when($request->filled('all'), function ($q) use ($page) {
            return $q->where('page_id', $page->id)->get();
        }, function ($q) {
            return $q->paginate(20);
        });

        return response([
            'records' => $tags
        ]);
    }

    /**
     * PageTagService index
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function getTagList($request, $page): Response
    {
        return response([
            'records' => PageTag::orderBy('sequence')->get()
        ]);
    }

    /**
     * PageTagService store
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function store($request, $pageIdentifier): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('tag', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:page_tags',
            'status' => 'required|boolean',
            'sequence' => 'required',
        ]);

        

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ], 403);
        }

        $tag = PageTag::create([
            'page_id' => $page->id,
            'title' => $request->title,
            'status' => $request->status,
            'sequence' => $request->sequence,
            'parent' => $request->parent,
        ]);
        
        $this->generateLog($request->user(), "created this page banner ({$tag->id})");

        $tag->load('images');

        return response([
            'record' => $tag
        ]);
    }

    /**
     * PageUvpService show
     * @param Request $request
     * @param Page $page
     * @param PageTag $tag
     * @return Response
     */
    public function show($request, $pageIdentifier, PageTag $tag): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('tag', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }
        
        $this->generateLog($request->user(), "viewed this page tag ({$tag->id})");

        return response([
            'record' => $tag
        ]);
    }

    /**
     * PageTagService update
     * @param Request $request
     * @param Page $page
     * @param PageTag $tag
     * @return Response
     */
    public function update($request, $pageIdentifier, PageTag $tag): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('tag', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:page_tags,title,'. $tag->id,
            'status' => 'required|boolean',
            'sequence' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ], 403);
        }

        $tag->update([
            'title' => $request->title,
            'status' => $request->status,
            'sequence' => $request->sequence,
            'parent' => $request->parent,
        ]);

        $this->generateLog($request->user(), "updated this page tag ({$tag->id})");

        return response([
            'record' => $tag
        ]);
    }

    /**
     * PageTagService destroy
     * @param Request $request
     * @param Page $page
     * @param PageTag $tag
     * @return Response
     */
    public function destroy($request, $pageIdentifier, PageTag $tag): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('tag', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }
        
        $this->generateLog($request->user(), "deleted this page tag ({$tag->id})");

        $tag->delete();

        return response([
            'record' => 'Page tag deleted successfully!'
        ]);
    }


}