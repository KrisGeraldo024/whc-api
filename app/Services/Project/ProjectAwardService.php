<?php

namespace App\Services\Project;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\ProjectAward;
use App\Traits\GlobalTrait; 

class ProjectAwardService
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
        $records = ProjectAward::orderBy('year', 'DESC')
        ->when(isset($request->keyword), function ($query) use ($request) {
            $query->where('content', 'LIKE', '%' . strtolower($request->keyword).'%');	
        })
        ->when($request->filled('project_id'), function ($query) use ($request) {
            $query->whereProjectId($request->project_id);
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
        $record = ProjectAward::create([
            'name'    => $request->name,
            'content'    => $request->content,
            'year'    => $request->year,
            'order'    => $request->order,
            'enabled'    => $request->enabled,
            'featured'    => $request->featured,
            'project_id'    => $request->project_id,
        ]);

        if ($request->hasFile('main_image')) {
            $this->addImages('project_award', $request, $record, 'main_image');
        }
        $this->generateLog($request->user(), "added this award ({$record->id}).");

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
    public function show ($project_award, $request): Response
    {
        $project_award->load('images');

        $this->generateLog($request->user(), "viewed this award ({$project_award->id}).");

        return response([
            'record' => $project_award
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($project_award, $request): Response
    {
        $project_award->update([
            'name'    => $request->name,
            'content'    => $request->content,
            'year'    => $request->year,
            'order'    => $request->order,
            'enabled'    => $request->enabled,
            'featured'    => $request->featured,
        ]);

        $this->updateImages('project_award', $request, $project_award, 'main_image');
        $this->generateLog($request->user(), "updated this award ({$project_award->id}).");

        return response([
            'record' => $project_award
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($project_award, $request): Response
    {
        $project_award->delete();
        $this->generateLog($request->user(), "deleted this award ({$project_award->id}).");
        return response([
            'record' => 'Award deleted'
        ]);
    }
}
