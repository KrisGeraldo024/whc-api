<?php

namespace App\Services\Page;

use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\{
    Validator,
    Facade,
};
use App\Models\{
    Page,
    PageTab
};
use App\Traits\GlobalTrait;

class PageTabServices
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * PageTabServices index
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function index($request, $identifier): Response
    {
        // return response([
        //     'records' => $request->all()
        // ], 403);
        // exit();

        $findPage = Page::whereIdentifier($identifier)->first();
        $tab = PageTab::orderBy('sequence')
            ->where('page_id', $findPage->id)
            ->when( $request->filled('all') , function ($q, $request) {
                return $q->get();
            }, function ($q) {
                return $q->paginate(20);
            });

        // $tab->modules = ($tab->modules ? json_decode($tab->modules) : []);

        return response([
            'records' => $tab
        ]);
    }

    /**
     * PageTabServices store
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function store($request, $pageIdentifier): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('tab', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'sometimes',
            'has_button' => 'required',
            'button_name' => $request->has_button ? 'required' : 'sometimes',
            'is_link_out' => $request->has_button ? 'required' : 'sometimes',
            'link' => $request->has_button ? 'required' : 'sometimes',
            'sequence' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ], 403);
        }

        $tab = PageTab::create([
            'page_id' => $page->id,
            'title' => $request->title,
            'description' => $request->description,
            'has_button' => $request->has_button,
            'button_name' => $request->has_button ? $request->button_name : null,
            'is_link_out' => $request->has_button ? $request->is_link_out : 0,
            'link' => $request->has_button ? $request->link : 0,
            'sequence' => $request->sequence,
            'has_color' => $request->has_color,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'tertiary_color' => $request->tertiary_color,
            'quaternary_color' => $request->quaternary_color,
        ]);

        // desktop image
        if ($request->has('desktop_image_upload_type')) {
            $this->imageUploader('page_tabs', $request, $tab, 'desktop_image', 'add');
        }
        // mobile image
        if ($request->has('mobile_image_upload_type')) {
            $this->imageUploader('page_tabs', $request, $tab, 'mobile_image', 'add');
        }
        
        $this->generateLog($request->user(), "created this page tab ({$tab->id})");
        $this->generateSystemLog($request->user(), "created this page tab ({$page->name} - {$tab->title})", 'cms', $tab);

        $tab->load('images');

        return response([
            'record' => $tab
        ]);
    }

    /**
     * PageTabServices show
     * @param Request $request
     * @param Page $page
     * @param PageTab $tab
     * @return Response
     */
    public function show($request, $pageIdentifier, PageTab $tab): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('tab', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }
        
        $this->generateLog($request->user(), "viewed this page tab ({$tab->id})");
        $this->generateSystemLog($request->user(), "viewed this page tab ({$page->name} - {$tab->title})", 'cms', $tab);

        $tab->load('images');

        return response([
            'record' => $tab
        ]);
    }

    /**
     * PageTabServices update
     * @param Request $request
     * @param Page $page
     * @param PageTab $tab
     * @return Response
     */
    public function update($request, $pageIdentifier, PageTab $tab): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('tab', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'sometimes',
            'has_button' => 'required',
            'button_name' => $request->has_button ? 'required' : 'sometimes',
            'is_link_out' => $request->has_button ? 'required' : 'sometimes',
            'link' => $request->has_button ? 'required' : 'sometimes',
            'sequence' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ], 403);
        }

        $tab->update([
            'title' => $request->title,
            'description' => $request->description,
            'has_button' => $request->has_button,
            'button_name' => $request->has_button ? $request->button_name : null,
            'is_link_out' => $request->has_button ? $request->is_link_out : 0,
            'link' => $request->has_button ? $request->link : 0,
            'sequence' => $request->sequence,
            'has_color' => $request->has_color,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'tertiary_color' => $request->tertiary_color,
            'quaternary_color' => $request->quaternary_color,
        ]);


        $this->updateImages('page_tabs', $request, $tab, 'desktop_image', 'update');

        $this->updateImages('page_tabs', $request, $tab, 'mobile_image', 'update');

        
        $this->generateLog($request->user(), "updated this page tab ({$tab->id})");
        $this->generateSystemLog($request->user(), "updated this page tab ({$page->name} - {$tab->title})", 'cms', $tab);

        $tab->load('images');

        return response([
            'record' => $tab
        ]);
    }

    /**
     * PageTabrService destroy
     * @param Request $request
     * @param Page $page
     * @param PageTab $tab
     * @return Response
     */
    public function destroy($request, $pageIdentifier, PageTab $tab): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('tab', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }
        
        $this->generateLog($request->user(), "deleted this page tab ({$tab->id})");
        $this->generateSystemLog($request->user(), "deleted this page tab ({$page->name} - {$tab->title})", 'cms', $tab);

        $tab->delete();

        return response([
            'record' => 'Page tab deleted successfully!'
        ]);
    }
}
