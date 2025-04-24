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
    PageCard
};
use App\Traits\GlobalTrait;

class PageCardService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * PageCardService index
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function index($request, $identifier): Response
    {
        $page = Page::whereIdentifier($identifier)->first();
        $cards = PageCard::orderBy('sequence')
        ->where('page_id', $page->id)
        ->when( $request->filled('all') , function ($q, $request) {
            return $q->get();
        }, function ($q) {
            return $q->paginate(20);
        });

        return response([
            'records' => $cards
        ]);
    }

    /**
     * PageCardService index
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function getLastSeq($request, $identifier) {
        $data = [];
        $page = Page::whereIdentifier($identifier)->first();
        //get latest articles
        $data = PageCard::where('page_id', $page->id)
        ->orderBy('sequence','desc')
        ->select('sequence')
        ->get();
        return $data;
    }

    /**
     * PageCardService store
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function store($request, $identifier): Response
    {
        $page = Page::whereIdentifier($identifier)->first();
        if (!in_array('card', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'sometimes',
            'has_button' => 'sometimes',
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

        $card = PageCard::create([
            'page_id' => $page->id,
            'title' => $request->title,
            'description' => $request->description,
            'has_button' => $request->has_button,
            'button_name' => $request->has_button ? $request->button_name : null,
            'is_link_out' => $request->has_button ? $request->is_link_out : 0,
            'link' => $request->link ? $request->link : '',
            'sequence' => $request->sequence,
            'card_type' => $request->card_type,
            'enabled' => $request->enabled ? 1 : 0,
        ]);

        if ($request->card_type === 'Promo') {
            $card = PageCard::create([
                'title' => $request->title,
                'description' => $request->description,
                'sequence' => $request->sequence,
                'featured' => $request->featured ? 1 : 0,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'two_comlumn' => $request->two_comlumn ? 1 : 0,
                'displaytime' => $request->displaytime ? 1 : 0,
                'primary_color' => $request->primary_color,  
                'secondary_color' => $request->secondary_color,  
            ]);
        }

        if ($request->has('main_image')) {
            $this->addImages('page_card', $request, $card, 'main_image');
        }
        if ($request->has('second_image')) {
            $this->addImages('page_card', $request, $card, 'second_image');
        }
        if ($request->has('third_image')) {
            $this->addImages('page_card', $request, $card, 'third_image');
        }

        $this->generateLog($request->user(), "created this page card ({$card->id})");

       

        return response([
            'record' => $card
        ]);
    }

    /**
     * PageCardService show
     * @param Request $request
     * @param Page $page
     * @param PageCard $card
     * @return Response
     */
    public function show($request, $identifier, PageCard $card): Response
    {
        $page = Page::whereIdentifier($identifier)->first();
        if (!in_array('card', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }
        
        $this->generateLog($request->user(), "viewed this page card ({$card->id})");

        $card->load('images');

        return response([
            'record' => $card
        ]);
    }

    /**
     * PageCardService update
     * @param Request $request
     * @param Page $page
     * @param PageCard $card
     * @return Response
     */
    public function update($request, $identifier, PageCard $card): Response
    {
        $page = Page::whereIdentifier($identifier)->first();
        if (!in_array('card', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'sometimes',
            'has_button' => 'sometimes',
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

        $card->update([
            'title' => $request->title,
            'description' => $request->description,
            'has_button' => $request->has_button,
            'button_name' => $request->has_button ? $request->button_name : null,
            'is_link_out' => $request->has_button ? $request->is_link_out : 0,
            'link' => $request->link ? $request->link : '',
            'sequence' => $request->sequence,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'card_type' => $request->card_type,
            'enabled' => $request->enabled ? 1 : 0,
        ]);

        if ($request->card_type === 'Promo') {
            $card->update([
                'title' => $request->title,
                'description' => $request->description,
                'sequence' => $request->sequence,
                'featured' => $request->featured ? 1 : 0,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'two_comlumn' => $request->two_comlumn ? 1 : 0,
                'displaytime' => $request->displaytime ? 1 : 0,
                'primary_color' => $request->primary_color,  
                'secondary_color' => $request->secondary_color, 
            ]);
        }
        if ($request->has('main_image')) {
            $this->updateImages('page_card', $request, $card, 'main_image');
        }
        if ($request->has('second_image')) {
            $this->updateImages('page_card', $request, $card, 'second_image');
        }
        if ($request->has('third_image')) {
            $this->updateImages('page_card', $request, $card, 'third_image');
        }
    
        // $this->updateImages('page_card', $request, $card, 'main_image');
        // $this->updateImages('page_card', $request, $card, 'mobile_image');

        $this->generateLog($request->user(), "updated this page card ({$card->id})");


        return response([
            'record' => $card
        ]);
    }

    /**
     * PageCardService destroy
     * @param Request $request
     * @param Page $page
     * @param PageCard $card
     * @return Response
     */
    public function destroy($request, $identifier, PageCard $card): Response
    {
        $page = Page::whereIdentifier($identifier)->first();
        if (!in_array('card', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }
        
        $this->generateLog($request->user(), "deleted this page card ({$card->id})");

        $card->delete();

        return response([
            'record' => 'Page card deleted successfully!'
        ]);
    }
}