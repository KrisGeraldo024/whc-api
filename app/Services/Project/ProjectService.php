<?php

namespace App\Services\Project;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Project;
use App\Models\Location;
use App\Traits\GlobalTrait;

class ProjectService
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
        $records = Project::orderBy('name','asc')
        ->when(isset($request->keyword), function ($query) use ($request) {
            return $query->where('name', 'LIKE', '%' . strtolower($request->keyword) . '%');
        })
        ->with('location', 'project_status', 'property')
        // ->when($request->filled('category'), function ($query) use ($request) {
        //     return $query->where('property_id', $request->category);
        // })

        ->when($request->filled('category'), function ($q) use ($request) {
            return $q->whereHas('property', function ($q) use ($request) {
                $q->where('id', $request->category)
                    ->orWhere('parent', $request->category);
            });
        })
        

        ->when($request->filled('project_status'), function ($query) use ($request) {
            return $query->where('project_status_id', $request->project_status);
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
        $record = Project::create([
           
            'name'              => $request->name,
            'slug'              => str_slug($request->name),
            
            'location_id'       => $request->location_id,
            'project_status_id' => $request->project_status_id,
            'property_id'        => $request->property_id,

            'property_tags'      => $request->property_tags,

            'architect'         => $request->architect,
            'partners'          => $request->partners,
            'land_area'         => $request->land_area,
            'phases'            => $request->phases,
            'full_address'      => $request->full_address,

            'title'             => $request->title,
            'content_col_1'     => $request->content_col_1,
            'content_col_2'     => $request->content_col_2,

            'virtual_tour_link'     => $request->virtual_tour_link,

            'contact_person'        => $request->contact_person,
            'position'              => $request->position,
            'contact_details'       => $request->contact_details,

            'enabled'           => $request->enabled,
            'featured'          => $request->featured,
            'order'             => $request->order,
            
           
        ]);

        if ($request->hasFile('main_image')) {
            $this->addImages('project', $request, $record, 'main_image');
        }

        if ($request->hasFile('banner_image')) {
            $this->addImages('project', $request, $record, 'banner_image');
        }

        if ($request->hasFile('location_map')) {
            $this->addImages('project', $request, $record, 'location_map');
        }

        if ($request->hasFile('project_logo')) {
            $this->addImages('project', $request, $record, 'project_logo');
        }

        $this->metatags($record, $request);

        $this->generateLog($request->user(), "added this project ({$record->id}).");

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
    public function show ($project, $request): Response
    {
        //$testimonial->load('images');

        $project->property_tags = json_decode($project->property_tags);

        $project->load('images', 'metadata');

        $this->generateLog($request->user(), "viewed this project ({$project->id}).");

        return response([
            'record' => $project
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($project, $request): Response
    {
        $project->update([
            'name'              => $request->name,
            'slug'              => str_slug($request->name),

            'location_id'       => $request->location_id,
            'project_status_id' => $request->project_status_id,
            'property_id'        => $request->property_id,

            'property_tags'      => $request->property_tags,

            'architect'         => $request->architect,
            'partners'          => $request->partners,
            'land_area'         => $request->land_area,
            'phases'            => $request->phases,
            'full_address'      => $request->full_address,

            'title'             => $request->title,
            'content_col_1'     => $request->content_col_1,
            'content_col_2'     => $request->content_col_2,

            'virtual_tour_link'     => $request->virtual_tour_link,

            'contact_person'        => $request->contact_person,
            'position'              => $request->position,
            'contact_details'       => $request->contact_details,

            'enabled'           => $request->enabled,
            'featured'          => $request->featured,
            'order'             => $request->order,
        ]);

        if ($request->hasFile('main_image')) {
            $this->updateImages('project', $request, $project, 'main_image');
        }

        if ($request->hasFile('banner_image')) {
            $this->updateImages('project', $request, $project, 'banner_image');
        }

        if ($request->hasFile('location_map')) {
            $this->updateImages('project', $request, $project, 'location_map');
        }

        if ($request->hasFile('project_logo')) {
            $this->updateImages('project', $request, $project, 'project_logo');
        }

        $this->metatags($project, $request);

        $this->generateLog($request->user(), "updated this project ({$project->id}).");

        return response([
            'record' => $project
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($project, $request): Response
    {
        $project->delete();
        $this->generateLog($request->user(), "deleted this project ({$project->id}).");

        return response([
            'record' => 'Project deleted'
        ]);
    }
}
