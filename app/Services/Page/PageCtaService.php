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
    PageCta
};
use App\Traits\GlobalTrait;

class PageCtaService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * PageCtaService index
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function index($request, $identifier): Response
    {
        $page = Page::whereIdentifier($identifier)->first();
        $ctas = PageCta::orderBy('sequence')
        ->where('page_id', $page->id)
        ->when( $request->filled('all') , function ($q, $request) {
            return $q->get();
        }, function ($q) {
            return $q->paginate(20);
        });

        return response([
            'records' => $ctas
        ]);
    }

    /**
     * PageCtaService store
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function store($request, $identifier): Response
    {
        $page = Page::whereIdentifier($identifier)->first();
        if (!in_array('cta', ($page->modules ? json_decode($page->modules) : [] ))) {
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

        $cta = PageCta::create([
            'page_id' => $page->id,
            'title' => $request->title,
            'description' => $request->description,
            'has_button' => $request->has_button,
            'button_name' => $request->has_button ? $request->button_name : null,
            'is_link_out' => $request->has_button ? $request->is_link_out : 0,
            'link' => $request->has_button ? $request->link : 0,
            'sequence' => $request->sequence,
        ]);

        // desktop image
        if ($request->has('main_image')) {
            $this->addImages('page_cta', $request, $cta, 'main_image');
        }
        if ($request->has('mobile_image')) {
            $this->addImages('page_cta', $request, $cta, 'mobile_image');
        }
       
        
        $this->generateLog($request->user(), "created this page cta ({$cta->id})");

        $cta->load('images');

        return response([
            'record' => $cta
        ]);
    }

    /**
     * PageCtaService show
     * @param Request $request
     * @param Page $page
     * @param PageCta $cta
     * @return Response
     */
    public function show($request, $identifier, PageCta $cta): Response
    {
        $page = Page::whereIdentifier($identifier)->first();
        if (!in_array('cta', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }
        
        $this->generateLog($request->user(), "viewed this page cta ({$cta->id})");

        $cta->load('images');

        return response([
            'record' => $cta
        ]);
    }

    /**
     * PageCtaService update
     * @param Request $request
     * @param Page $page
     * @param PageCta $cta
     * @return Response
     */
    public function update($request, $identifier, PageCta $cta): Response
    {
        $page = Page::whereIdentifier($identifier)->first();
        if (!in_array('cta', ($page->modules ? json_decode($page->modules) : [] ))) {
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

        $cta->update([
            'title' => $request->title,
            'description' => $request->description,
            'has_button' => $request->has_button,
            'button_name' => $request->has_button ? $request->button_name : null,
            'is_link_out' => $request->has_button ? $request->is_link_out : 0,
            'link' => $request->has_button ? $request->link : 0,
            'sequence' => $request->sequence,
        ]);

        // desktop image
        if ($request->has('main_image')) {
        $this->updateImages('page_cta', $request, $cta, 'main_image');
        }
        if ($request->has('mobile_image')) {
        $this->updateImages('page_cta', $request, $cta, 'mobile_image');
        }
        
        $this->generateLog($request->user(), "updated this page cta ({$cta->id})");

        $cta->load('images');

        return response([
            'record' => $cta
        ]);
    }

    /**
     * PageCtaService destroy
     * @param Request $request
     * @param Page $page
     * @param PageCta $cta
     * @return Response
     */
    public function destroy($request, $identifier, PageCta $cta): Response
    {
        $page = Page::whereIdentifier($identifier)->first();
        if (!in_array('cta', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }
        
        $this->generateLog($request->user(), "deleted this page cta ({$cta->id})");

        $cta->delete();

        return response([
            'record' => 'Page cta deleted successfully!'
        ]);
    }
}