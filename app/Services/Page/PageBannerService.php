<?php

namespace App\Services\Page;

use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\{
    Validator,
    Facade,
};
use App\Models\{
    BusinessUnit,
    Page,
    PageBanner
};
use App\Traits\GlobalTrait;

class PageBannerService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * PageBannerService index
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function index($request, $identifier): Response
    {
        $page = Page::whereIdentifier($identifier)->first();
        $banners = PageBanner::orderBy('sequence')
        ->where('page_id', $page->id)
        ->when( $request->filled('all') , function ($q, $request) {
            return $q->get();
        }, function ($q) {
            return $q->paginate(20);
        });

        return response([
            'records' => $banners
        ]);
    }

    /**
     * PageBannerService store
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function store($request, $pageIdentifier): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('banner', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'sometimes',
            'has_button' => 'sometimes',
            'button_name' =>  'sometimes',
            'is_link_out' => 'sometimes',
            'link' =>  'sometimes',
            'sequence' => 'required',
            'banner_type' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ], 403);
        }

        $record = PageBanner::create([
            'page_id' => $page->id,
            'title' => $request->title,
            'description' => $request->description,
            'has_button' => $request->has_button,
            'button_name' => $request->button_name ? $request->button_name : null,
            'is_link_out' => $request->is_link_out ? $request->is_link_out : 0,
            'link' => $request->link ? $request->link : '',
            'sequence' => $request->sequence,
            'banner_type' => $request->banner_type,
            'primary_color' => $request->primary_color,  
        ]);

       
        if ($request->has('main_image')) {
            $this->addImages('page_banner', $request, $record, 'main_image');
        }
        if ($request->has('mobile_image')) {
            $this->addImages('page_banner', $request, $record, 'mobile_image');
        }
     
        
        $this->generateLog($request->user(), "created this page banner ({$record->id})");
       

        //$banner->load('images');

        return response([
            'record' => $record
        ]);
    }

    /**
     * PageBannerService show
     * @param Request $request
     * @param Page $page
     * @param PageBanner $banner
     * @return Response
     */
    public function show($request, $pageIdentifier, PageBanner $banner): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('banner', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }
        
        $this->generateLog($request->user(), "viewed this page banner ({$banner->id})");

        $banner->load('images');

        return response([
            'record' => $banner
        ]);
    }

    /**
     * PageBannerService update
     * @param Request $request
     * @param Page $page
     * @param PageBanner $banner
     * @return Response
     */
    public function update($request, $pageIdentifier, PageBanner $banner): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('banner', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'sometimes',
            'has_button' => 'sometimes',
            'button_name' => 'sometimes',
            'is_link_out' =>  'sometimes',
            'link' =>  'sometimes',
            'sequence' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ], 403);
        }

        if ($request->banner_type === 'Homepage Banner') {
            $featured_business = BusinessUnit::where('featured', 1);
            $featured_business->update([
                'featured' => 0
            ]);
            $business_unit = BusinessUnit::find($request->business_unit_id);
            $business_unit->update([
                'featured' => 1
            ]);
        }

        $banner->update([
            'title' => $request->title,
            'description' => $request->description,
            'has_button' => $request->has_button,
            'button_name' => $request->has_button ? $request->button_name : null,
            'is_link_out' => $request->has_button ? $request->is_link_out : 0,
            'link' => $request->has_button ? $request->link : 0,
            'sequence' => $request->sequence,
            'banner_type' => $request->banner_type,
            'primary_color' => $request->primary_color,  
        ]);

        // desktop image

        $this->updateImages('page_banner', $request, $banner, 'main_image');
        $this->updateImages('page_banner', $request, $banner, 'mobile_image');


        $this->generateLog($request->user(), "updated this page banner ({$banner->id})");

     

        return response([
            'record' => $banner
        ]);
    }

    /**
     * PageBannerService destroy
     * @param Request $request
     * @param Page $page
     * @param PageBanner $banner
     * @return Response
     */
    public function destroy($request, $pageIdentifier, PageBanner $banner): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('banner', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }
        
        $this->generateLog($request->user(), "deleted this page banner ({$banner->id})");

        $banner->delete();

        return response([
            'record' => 'Page banner deleted successfully!'
        ]);
    }
}
