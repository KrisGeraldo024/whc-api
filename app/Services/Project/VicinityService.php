<?php

namespace App\Services\Project;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Vicinity;
use App\Models\Project;
use App\Models\Location;
use App\Traits\GlobalTrait;

class VicinityService
{
    use GlobalTrait;
    
    public function index ($request): Response
    {
        $records = Vicinity::orderBy('order')
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
        $record = Vicinity::create([
           
            'content'          => $request->content,
            'order'            => $request->order,
            'project_id'            => $request->project_id,
           
        ]);

        $this->generateLog($request->user(), "added this vicinity ({$record->id}).");

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
    public function show ($vicinity, $request): Response
    {
        //$testimonial->load('images');
        //$vicinity->load('images', 'metadata');

        $this->generateLog($request->user(), "viewed this vicinity ({$vicinity->id}).");

        return response([
            'record' => $vicinity
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($vicinity, $request): Response
    {
        $vicinity->update([
            'content'          => $request->content,
            'order'            => $request->order,
            'project_id'       => $request->project_id,
        ]);

      
        $this->generateLog($request->user(), "updated this project ({$vicinity->id}).");

        return response([
            'record' => $vicinity
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($vicinity, $request): Response
    {
        $vicinity->delete();
        $this->generateLog($request->user(), "deleted this project ({$vicinity->id}).");

        return response([
            'record' => 'Project deleted'
        ]);
    }
}
