<?php

namespace App\Services\Philosophy;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Philosophy;
use App\Traits\GlobalTrait;

class PhilosophyService
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
        $records = Philosophy::orderBy('order')
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
        $record = Philosophy::create([
            'title'      => $request->title,
            'subtitle'   => $request->subtitle,
            'order'     => $request->order,

        ]);

        if ($request->hasFile('icon')) {
            $this->addImages('philosophy', $request, $record, 'icon');
            $this->metatags($record, $request);
        }
        if ($request->hasFile('main_image')) {
            $this->addImages('philosophy', $request, $record, 'main_image');
        }
        $this->generateLog($request->user(), "added this philosophy ({$record->id}).");

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
    public function show ($philosophy, $request): Response
    {
        $philosophy->load('images', 'metadata');

        $this->generateLog($request->user(), "viewed this philosophy ({$philosophy->id}).");

        return response([
            'record' => $philosophy
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($philosophy, $request): Response
    {
        $philosophy->update([
            'title'      => $request->title ,
            'subtitle'   => $request->subtitle,
            'order'     => $request->order,
        ]);

        $this->updateImages('philosophy', $request, $philosophy, 'icon');
        
        if ($request->hasFile('main_image')) {
            $this->updateImages('philosophy', $request, $philosophy, 'main_image');
        }
        $this->metatags($philosophy, $request);

        $this->generateLog($request->user(), "updated this philosophy ({$philosophy->id}).");

        return response([
            'record' => $philosophy
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($philosophy, $request): Response
    {
        $philosophy->delete();
        $this->generateLog($request->user(), "deleted this philosophy ({$philosophy->id}).");

        return response([
            'record' => 'Core Values deleted'
        ]);
    }
}
