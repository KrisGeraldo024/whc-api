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
    PageUvp
};
use App\Traits\GlobalTrait;


class PageUvpService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * PageUvpService index
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function index($request, $identifier): Response
    {
        $page = Page::whereIdentifier($identifier)->first();
        $uvps = PageUvp::orderBy('sequence')
        ->where('page_id', $page->id)
        ->when( $request->filled('all') , function ($q, $request) {
            return $q->get();
        }, function ($q) {
            return $q->paginate(20);
        });

        return response([
            'records' => $uvps
        ]);
    }

    /**
     * PageUvpService store
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function store($request, $pageIdentifier): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('uvp', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'sometimes',
            'button_name' =>'sometimes',
            'is_link_out' => 'sometimes',
            'link' => 'sometimes',
            'sequence' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ], 403);
        }

        $uvp = PageUvp::create([
            'page_id' => $page->id,
            'title' => $request->title,
            'description' => $request->description,
            'has_button' => $request->has_button,
            'button_name' => $request->has_button ? $request->button_name : null,
            'is_link_out' => $request->has_button ? $request->is_link_out : 0,
            'link' => $request->has_button ? $request->link : 0,
            'sequence' => $request->sequence,
            'uvp_type' => $request->uvp_type,
        ]);

        if ($request->has('main_image')) {
            $this->addImages('page_uvp', $request, $uvp, 'main_image');
        }
        if ($request->has('mobile_image')) {
            $this->addImages('page_uvp', $request, $uvp, 'mobile_image');
        }


        // desktop image
        // if ($request->has('desktop_image_upload_type')) {
        //     $this->imageUploader('page_uvp', $request, 'desktop_image', 'add');
        // }
        // // mobile image
        // if ($request->has('mobile_image_upload_type')) {
        //     $this->imageUploader('page_uvp', $request, 'mobile_image', 'add');
        // }
        
        $this->generateLog($request->user(), "created this page banner ({$uvp->id})");
        // $this->generateSystemLog($request->user(), "created this page banner ({$page->name} - {$uvp->title})", 'cms', $uvp);

        $uvp->load('images');

        return response([
            'record' => $uvp
        ]);
    }

    /**
     * PageUvpService show
     * @param Request $request
     * @param Page $page
     * @param PageUvp $uvp
     * @return Response
     */
    public function show($request, $pageIdentifier, PageUvp $uvp): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('uvp', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }
        
        $this->generateLog($request->user(), "viewed this page uvp ({$uvp->id})");
        // $this->generateSystemLog($request->user(), "viewed this page uvp ({$page->name} - {$uvp->title})", 'cms', $uvp);

        $uvp->load('images');

        return response([
            'record' => $uvp
        ]);
    }

    /**
     * PageUvpService update
     * @param Request $request
     * @param Page $page
     * @param PageUvp $uvp
     * @return Response
     */
    public function update($request, $pageIdentifier, PageUvp $uvp): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('uvp', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'sometimes',
            'button_name' => 'sometimes',
            'is_link_out' =>  'sometimes',
            'link' => 'sometimes',
            'sequence' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ], 403);
        }

        $uvp->update([
            'title' => $request->title,
            'description' => $request->description,
            'has_button' => $request->has_button,
            'button_name' => $request->has_button ? $request->button_name : null,
            'is_link_out' => $request->has_button ? $request->is_link_out : 0,
            'link' => $request->has_button ? $request->link : 0,
            'sequence' => $request->sequence,
            'uvp_type' => $request->uvp_type,
        ]);

            
        $this->updateImages('page_uvp', $request, $uvp, 'main_image');
        $this->updateImages('page_uvp', $request, $uvp, 'mobile_image');


                
        $this->generateLog($request->user(), "updated this page uvp ({$uvp->id})");
        // $this->generateSystemLog($request->user(), "updated this page uvp ({$page->name} - {$uvp->title})", 'cms', $uvp);

        $uvp->load('images');

        return response([
            'record' => $uvp
        ]);
    }

    /**
     * PageUvpService destroy
     * @param Request $request
     * @param Page $page
     * @param PageUvp $uvp
     * @return Response
     */
    public function destroy($request, $pageIdentifier, PageUvp $uvp): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('uvp', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }
        
        $this->generateLog($request->user(), "deleted this page uvp ({$uvp->id})");
        // $this->generateSystemLog($request->user(), "deleted this page uvp ({$page->name} - {$uvp->title})", 'cms', $uvp);

        $uvp->delete();

        return response([
            'record' => 'Page uvp deleted successfully!'
        ]);
    }


}