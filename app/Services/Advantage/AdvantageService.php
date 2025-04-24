<?php

namespace App\Services\Advantage;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Advantage;
use App\Traits\GlobalTrait;

class AdvantageService
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
        $records = Advantage::orderBy('order')
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
        $record = Advantage::create([
            'title'      => $request->title,
            'content'   => $request->content,
            'order'     => $request->order,

        ]);

        if ($request->hasFile('main_image')) {
            $this->addImages('advantage', $request, $record, 'main_image');
            $this->metatags($record, $request);
        }
        $this->generateLog($request->user(), "added this advantage ({$record->id}).");

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
    public function show ($advantage, $request): Response
    {
        //$testimonial->load('images');
        $advantage->load('images', 'metadata');

        $this->generateLog($request->user(), "viewed this advantage ({$advantage->id}).");

        return response([
            'record' => $advantage
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($advantage, $request): Response
    {
        $advantage->update([
            'title'      => $request->title ,
            'content'   => $request->content,
            'order'     => $request->order,
        ]);

        $this->updateImages('advantage', $request, $advantage, 'main_image');
        $this->metatags($advantage, $request);

        $this->generateLog($request->user(), "updated this advantage ({$advantage->id}).");

        return response([
            'record' => $advantage
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($advantage, $request): Response
    {
        $advantage->delete();
        $this->generateLog($request->user(), "deleted this advantage ({$advantage->id}).");

        return response([
            'record' => 'CLI advantage deleted'
        ]);
    }
}
