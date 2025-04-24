<?php

namespace App\Services\Award;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Award;
use App\Traits\GlobalTrait; 

class AwardService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * FaqService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = Award::orderBy(isset($request->sortBy) ? $request->sortBy : 'date', isset($request->sortDirection) ? $request->sortDirection : 'desc')
        // ->orderBy('order')
        ->when(isset($request->keyword), function ($query) use ($request) {
            $query->where('name', 'LIKE', '%' . strtolower($request->keyword).'%');	
        })
        ->when($request->filled('all') , function ($query, $request) {
            return $query->get();
        }, function ($query) {
            return $query->orderBy('order')->paginate(20);
        });

        return response([
            'records' => $records
        ]);
    }
    /**
     * FaqService index
     * @param  Request $request
     * @return Response
     */
    public function getAwards ($request): Response
    {
        if ($request->query('year')) {
            $records = Award::where('year', $request->query('year'))
                ->orderBy('order', $request->query('order') ? $request->query('order') : 'asc')
                ->with('images')
                ->paginate(8);
        } else {
            $records = Award::orderBy('order', ($request->query('order') ? $request->query('order') : 'asc'))
                ->with('images')
                ->paginate(8);
        }
        

        return response([
            'records' => $records
        ]);
    }

    /**
     * FaqService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $record = Award::create([
            'name'    => $request->name,
            'awarding_body'    => $request->awarding_body,
            'date'    => $request->date,
            'order'    => $request->order ?? Award::count() + 1,
            'enabled'    => $request->enabled ?? 1,
        ]);

        if ($request->hasFile('main_image')) {
            $this->addImages('award', $request, $record, 'main_image');
        }
        $this->metatags($record, $request);
        $this->generateLog($request->user(), "Created", "Awards", $record);

        return response([
            'record' => $record
        ]);
    }

    /**
     * FaqService show
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function show ($award, $request): Response
    {
        //$testimonial->load('images');
        $award->load('images', 'metadata');

        // $this->generateLog($request->user(), "viewed this award ({$award->id}).");

        return response([
            'record' => $award
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($award, $request): Response
    {
        $award->update([
            'name'    => $request->name,
            'awarding_body'    => $request->awarding_body,
            'date'    => $request->date,
            'order'    => $request->order ?? $award->order,
            'enabled'    => $request->enabled ?? 1,
        ]);

        // if($request->has('main_image')) {
            $this->updateImages('award', $request, $award, 'main_image');
        // }
        $this->metatags($award, $request);

        $award->load(['images', 'metadata']);

        $this->generateLog($request->user(), "Changed", "Awards", $award);

        return response([
            'record' => $award
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($award, $request): Response
    {
        if ($award->order !== Award::max('order')) {
            Award::where('order', '>', $award->order)->decrement('order'); 
        }
        $this->generateLog($request->user(), "Deleted", "Awards", $award);
        $award->delete();
        $this->reassignOrderValues('Award');

        return response([
            'record' => 'Award deleted'
        ]);
    }
}
