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
        $records = Testimonial::orderBy('order')
        ->when(isset($request->keyword), function ($query) use ($request) {
            $query->where('content', 'LIKE', '%' . strtolower($request->keyword).'%');	
        })
        ->when($request->filled('all') , function ($query, $request) {
            return $query->get();
        }, function ($query) {
            return $query->paginate(20);
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
            'content'   => $request->content,
            'property'  => $request->property,
            'order'     => $request->order,
            'enabled'   => $request->enabled,
            'category'  => $request->category,
        ]);

        if ($request->hasFile('main_image')) {
            $this->addImages('testimonial', $request, $record, 'main_image');
            $this->metatags($record, $request);
        }
        $this->generateLog($request->user(), "added this testimonial ({$record->id}).");

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
        $testimonial->load('images', 'metadata');

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
            'content'   => $request->content,
            'property'  => $request->property,
            'order'     => $request->order,
            'enabled'   => $request->enabled,
            'category'  => $request->category,
        ]);

        $this->updateImages('testimonial', $request, $testimonial, 'main_image');
        $this->metatags($testimonial, $request);

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
        $testimonial->delete();
        $this->generateLog($request->user(), "deleted this testimonial ({$testimonial->id}).");

        return response([
            'record' => 'Testimonial deleted'
        ]);
    }
}
