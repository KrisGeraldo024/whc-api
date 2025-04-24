<?php

namespace App\Services\Philosophy;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\MissionVision;
use App\Traits\GlobalTrait;

class MissionVisionService
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
        $records = MissionVision::when(isset($request->keyword), function ($query) use ($request) {
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
      
        $record = MissionVision::create([
            'title'    => $request->title,
            'content'    => $request->content,
            'enabled'    => $request->enabled,
        ]);

        $this->addImages('mission_vision', $request, $record, 'main_image');
        
        $this->generateLog($request->user(), "added this mission vision ({$record->id}).");

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
    public function show ($mission_vision, $request): Response
    {
        $mission_vision->load('images', 'metadata');

        $this->generateLog($request->user(), "viewed this mission vision ({$mission_vision->id}).");

        return response([
            'record' => $mission_vision
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($mission_vision, $request): Response
    {

        $mission_vision->update([
            'title'    => $request->title,
            'content'    => $request->content,
            'enabled'    => $request->enabled,
        ]);

        $this->updateImages('mission_vision', $request, $mission_vision, 'main_image');

        $this->generateLog($request->user(), "updated this mission vision ({$mission_vision->id}).");

        return response([
            'record' => $mission_vision
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($mission_vision, $request): Response
    {
        $mission_vision->delete();
        $this->generateLog($request->user(), "deleted this mission vision ({$mission_vision->id}).");

        return response([
            'record' => 'Mission Vision deleted'
        ]);
    }
}
