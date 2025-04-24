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
    PageFile,
    PageTag
};
use App\Traits\GlobalTrait;


class PageFileService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * PageFileService index
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function index($request, $identifier): Response
    {
        $page = Page::whereIdentifier($identifier)->first();
        $files = PageFile::orderBy('date_published','desc')
            ->orderBy('title', 'desc')
            ->with(['tag'])
            ->where('page_id', $page->id)
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where(function ($subquery) use ($request) {
                    $subquery->where('title', 'LIKE', '%' . strtolower($request->q) . '%');
                });
            })
        ->when( $request->filled('all') , function ($q, $request) {
            return $q->get();
        }, function ($q) {
            return $q->paginate(20);
        });

        return response([
            'records' => $files
        ]);
    }

    /**
     * PageFileService store
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function store($request, $pageIdentifier): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('file', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'tag_id' => 'required',
            // 'sequence' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ], 403);
        }

        // $exist = Asset::where('name', $request->file[0]->getClientOriginalName())->first();

        // if ($exist) {
        //     return response([
        //         'errors' => ['File name already exist!'],
        //     ], 403);
        // }

        $file = PageFile::create([
            'page_id' => $page->id,
            'title' => $request->title,
            'date_published' => $request->date_published,
            'tag_id' => $request->tag_id 
            // 'sequence' => $request->sequence,
        ]);

        $this->addImages('page_file', $request, $file, 'page_file', 'file');        
        
        $this->generateLog($request->user(), "created this page file ({$file->id})");


        //$file->load('files');

        return response([
            'record' => $file,
        ]);
    }

    /**
     * PageFileService show
     * @param Request $request
     * @param Page $page
     * @param PageFile $file
     * @return Response
     */
    public function show($request, $pageIdentifier, PageFile $file): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('file', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }
        
        $this->generateLog($request->user(), "viewed this page file ({$file->id})");
        // $this->generateSystemLog($request->user(), "viewed this page file ({$page->name} - {$file->title})", 'cms', $file);

        $file->load('files')
        ->with(['tag']);

        $tags = PageTag::where('page_id',$page->id)
            ->get();

        return response([
            'record' => $file,
            'tags' => $tags
        ]);
    }

    /**
     * PageFileService update
     * @param Request $request
     * @param Page $page
     * @param PageFile $file
     * @return Response
     */
    public function update($request, $pageIdentifier, PageFile $file): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('file', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'tag_id' => 'required',
            // 'sequence' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ], 403);
        }

        $file->update([
            'title' => $request->title,
            'date_published' => $request->date_published,
            'tag_id' => $request->tag_id,
            // 'sequence' => $request->sequence,
        ]);

        $this->updateImages('page_file', $request, $file, 'page_file','file');

        //$this->updateImages('page_file', $request, $file, 'page_file', 'file');
        // $this->imageUploader('page_file', $request, $file, 'page_file', 'update', 'file');
        
        $this->generateLog($request->user(), "updated this page file ({$file->id})");

        //$file->load('files');

        return response([
            'record' => $file
        ]);
    }

    /**
     * PageFileService destroy
     * @param Request $request
     * @param Page $page
     * @param PageFile $file
     * @return Response
     */
    public function destroy($request, $pageIdentifier, PageFile $file): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('file', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }
        
        $this->generateLog($request->user(), "deleted this page file ({$file->id})");
        //$this->generateSystemLog($request->user(), "deleted this page file ({$page->name} - {$file->title})", 'cms', $file);

        $file->delete();

        return response([
            'record' => 'Page file deleted successfully!'
        ]);
    }


}