<?php

namespace App\Services\Project;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\ConstructionUpdate;
use App\Traits\GlobalTrait;

class ConstructionUpdateService
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
        $records = ConstructionUpdate::when(isset($request->keyword), function ($query) use ($request) {
            $query->where('content', 'LIKE', '%' . strtolower($request->keyword) . '%');
        })
        ->when($request->filled('project_id'), function ($query) use ($request) {
            $query->whereProjectId($request->project_id);
        })
        ->when($request->filled('all'), function ($query, $request) {
            return $query->orderBy('date', 'desc')->get();
        }, function ($query) {
            return $query->orderBy('date', 'desc')->paginate(20);
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
       
        $record = ConstructionUpdate::create([
            'date'          => $request->date,
            'enabled'       => $request->enabled,
            'project_id'    => $request->project_id,
            
        ]);

        if ($request->hasFile('gallery')) {
            $this->addImages('construction_update', $request, $record, 'gallery');
        }

        $this->generateLog($request->user(), "added this construction update ({$record->id}).");
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
    public function show ($construction_update, $request): Response
    {
        $construction_update->load('images');
        $this->generateLog($request->user(), "viewed this construction update ({$construction_update->id}).");
        return response([
            'record' => $construction_update
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($construction_update, $request): Response
    {
        $construction_update->update([
            'date'          => $request->date,
            'enabled'       => $request->enabled,
            'project_id'    => $request->project_id,
        ]);

        $this->updateImages('construction_update', $request, $construction_update, 'gallery');
        $this->generateLog($request->user(), "updated this construction update ({$construction_update->id}).");
        return response([
            'record' => $construction_update
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($construction_update, $request): Response
    {
        $construction_update->delete();
        $this->generateLog($request->user(), "deleted this construction update ({$construction_update->id}).");
        return response([
            'record' => 'Construction update deleted'
        ]);
    }
}
