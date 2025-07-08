<?php

namespace App\Services\Feedback;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Testimonial;
use App\Traits\GlobalTrait;

class TestimonialService
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
        $records = Testimonial::orderBy(isset($request->sortBy) ? $request->sortBy : 'date', isset($request->sortDirection) ? $request->sortDirection : 'desc')
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
     * FaqService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $record = Testimonial::create([
            'name'      => $request->name,
            'description'   => $request->description,
            'position'  => $request->position,
            'order'    => $request->order ?? Testimonial::count() + 1,
            'enabled'   => $request->enabled ?? 1,
        ]);

        $this->metatags($record, $request);
        $this->generateLog($request->user(), "Created", "Testimonial", $record);

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
    public function show ($testimonial, $request): Response
    {
        //$testimonial->load('images');
        // $testimonial->load('images', 'metadata');

        $this->generateLog($request->user(), "viewed this testimonial ({$testimonial->id}).");

        return response([
            'record' => $testimonial
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($testimonial, $request): Response
    {
        $testimonial->update([
            'name'      => $request->name,
            'description'   => $request->description,
            'position'  => $request->position,
            'order'     => $request->order ?? $testimonial->order,
            'enabled'    => $request->enabled ?? 1,
        ]);

        $this->generateLog($request->user(), "updated this testimonial ({$testimonial->id}).");

        return response([
            'record' => $testimonial
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($testimonial, $request): Response
    {
        if ($testimonial->order !== Testimonial::max('order')) {
            Testimonial::where('order', '>', $testimonial->order)->decrement('order'); 
        }
        $this->generateLog($request->user(), "Deleted", "Testimonials", $testimonial);
        $testimonial->delete();
        $this->reassignOrderValues('Testimonial');

        return response([
            'record' => 'Testimonial deleted'
        ]);
    }
}
