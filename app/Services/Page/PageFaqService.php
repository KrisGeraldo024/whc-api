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
    PageFaq
};
use App\Traits\GlobalTrait;

class PageFaqService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * PageFaqService index
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function index($request, $identifier): Response
    {
        $page = Page::whereIdentifier($identifier)->first();
        $faqs = PageFaq::orderBy('sequence')
        ->where('page_id', $page->id)
        ->when( $request->filled('all') , function ($q, $request) {
            return $q->get();
        }, function ($q) {
            return $q->paginate(20);
        });

        return response([
            'records' => $faqs
        ]);
    }

    /**
     * PageFaqService store
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function store($request, $pageIdentifier): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('faq', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'answer' => 'required',
            'sequence' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ], 403);
        }

        $faq = PageFaq::create([
            'page_id' => $page->id,
            'title' => $request->title,
            'answer' => $request->answer,
            'sequence' => $request->sequence,
        ]);

        if ($request->has('main_image')) {
            $this->addImages('page_faq', $request, $faq, 'main_image');
        }
        
        $this->generateLog($request->user(), "created this page faq ({$faq->id})");

        return response([
            'record' => $faq
        ]);
    }

    /**
     * PageFaqService show
     * @param Request $request
     * @param Page $page
     * @param PageFaq $faq
     * @return Response
     */
    public function show($request, $pageIdentifier, PageFaq $faq): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('faq', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }

        $faq->load('images');
        
        $this->generateLog($request->user(), "viewed this page faq ({$faq->id})");

        return response([
            'record' => $faq
        ]);
    }

    /**
     * PageFaqService update
     * @param Request $request
     * @param Page $page
     * @param PageFaq $faq
     * @return Response
     */
    public function update($request, $pageIdentifier, PageFaq $faq): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('faq', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'answer' => 'required',
            'sequence' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ], 403);
        }

        $faq->update([
            'title' => $request->title,
            'answer' => $request->answer,
            'sequence' => $request->sequence,
        ]);

        if ($request->has('main_image')) {
            $this->updateImages('page_faq', $request, $faq, 'main_image');
        }

        $this->generateLog($request->user(), "updated this page faq ({$faq->id})");

        return response([
            'record' => $faq
        ]);
    }

    /**
     * PageFaqService destroy
     * @param Request $request
     * @param Page $page
     * @param PageFaq $faq
     * @return Response
     */
    public function destroy($request, $pageIdentifier, PageFaq $faq): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('faq', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }
        
        $this->generateLog($request->user(), "deleted this page faq ({$faq->id})");

        $faq->delete();

        return response([
            'record' => 'Page faq deleted successfully!'
        ]);
    }
}