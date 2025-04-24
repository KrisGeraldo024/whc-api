<?php

namespace App\Services\Project;

use App\Models\Architect;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};


use App\Traits\GlobalTrait;


class ArchitectService
{
    use GlobalTrait;
    
    public function index ($request): Response
    {
        $records = Architect::orderBy('order')
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
        $record = Architect::create([
           
            'title'          => $request->title,
            'order'            => $request->order,
            'project_id'       => $request->project_id,
            
        ]);

        if ($request->hasFile('gallery')) {
            $this->addImages('architect', $request, $record, 'gallery');
        }

        $this->generateLog($request->user(), "added this ArchitectPerspective ({$record->id}).");

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
    public function show ($architect, $request): Response
    {
        //$testimonial->load('images');
        $architect->load('images');

        $this->generateLog($request->user(), "viewed this ArchitectPerspective ({$architect->id}).");

        return response([
            'record' => $architect
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($architect, $request): Response
    {
        $architect->update([
            'title'          => $request->title,
            'order'          => $request->order,
            'project_id'     => $request->project_id,
        ]);

        $this->updateImages('architect', $request, $architect, 'gallery');
      
        $this->generateLog($request->user(), "updated this architect ({$architect->id}).");

        return response([
            'record' => $architect
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($architect_perspective, $request): Response
    {
        $architect_perspective->delete();
        $this->generateLog($request->user(), "deleted this ArchitectPerspective ({$architect_perspective->id}).");

        return response([
            'record' => 'Project deleted'
        ]);
    }
}
