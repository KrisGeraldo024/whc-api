<?php

namespace App\Services\Project;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Vicinity;
use App\Models\Project;
use App\Models\Location;
use App\Models\Amenity;

use App\Traits\GlobalTrait;

class AmenityService
{
    use GlobalTrait;
    
    public function index ($request): Response
    {
        $records = Amenity::orderBy('order')
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
        $record = Amenity::create([
           
            'content'          => $request->content,
            'order'            => $request->order,
            'project_id'       => $request->project_id,
           
        ]);

        if ($request->hasFile('main_image')) {
            $this->addImages('amenity', $request, $record, 'main_image');
        }


        $this->generateLog($request->user(), "added this Amenity ({$record->id}).");

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
    public function show ($amenity, $request): Response
    {
        //$testimonial->load('images');
        $amenity->load('images');

        $this->generateLog($request->user(), "viewed this amenity ({$amenity->id}).");

        return response([
            'record' => $amenity
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($amenity, $request): Response
    {
        $amenity->update([
            'content'          => $request->content,
            'order'            => $request->order,
            'project_id'       => $request->project_id,
        ]);


        $this->updateImages('amenity', $request, $amenity, 'main_image');

        $this->generateLog($request->user(), "updated this amenity ({$amenity->id}).");

        return response([
            'record' => $amenity
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($amenity, $request): Response
    {
        $amenity->delete();
        $this->generateLog($request->user(), "deleted this amenity ({$amenity->id}).");

        return response([
            'record' => 'Project deleted'
        ]);
    }
}
