<?php

namespace App\Services\Project;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Vicinity;
use App\Models\Project;
use App\Models\Location;
use App\Models\Floorplan;

use App\Traits\GlobalTrait;

class FloorplanService
{
    use GlobalTrait;
    
    public function index ($request): Response
    {
        $records = Floorplan::orderBy('order')
        ->when($request->filled('project_id'), function ($query) use ($request) {
            $query->whereProjectId($request->project_id);
        })
       
        ->when($request->filled('all'), function ($query) {
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
        $record = Floorplan::create([
           
            'description'      => $request->description,
            'order'            => $request->order,
            'project_id'       => $request->project_id,
           
        ]);

        if ($request->hasFile('gallery')) {
        $this->addImages('floorplan', $request, $record, 'gallery');
        }
        
        $this->generateLog($request->user(), "added this Floorplan ({$record->id}).");

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
    public function show ($floorplan, $request): Response
    {
        //$testimonial->load('images');
        $floorplan->load('images');

        $this->generateLog($request->user(), "viewed this floorplan ({$floorplan->id}).");

        return response([
            'record' => $floorplan
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($floorplan, $request): Response
    {
        $floorplan->update([
            'description'      => $request->description,
            'order'            => $request->order,
            'project_id'       => $request->project_id,
        ]);

        $this->updateImages('floorplan', $request, $floorplan, 'gallery');
      
        $this->generateLog($request->user(), "updated this floorplan ({$floorplan->id}).");

        return response([
            'record' => $floorplan
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($floorplan, $request): Response
    {
        $floorplan->delete();
        $this->generateLog($request->user(), "deleted this floorplan ({$floorplan->id}).");

        return response([
            'record' => 'Floorplan deleted'
        ]);
    }
}