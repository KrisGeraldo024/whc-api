<?php

namespace App\Services\Project;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\ProjectStatus;
use App\Traits\GlobalTrait;

class ProjectStatusService
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
        $records = ProjectStatus::when(isset($request->keyword), function ($query) use ($request) {
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
        $validator = Validator::make($request->all(), [
            'name'    => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = ProjectStatus::create([
            'name'    => $request->name,
        ]);

        $this->generateLog($request->user(), "added this project status ({$record->id}).");

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
    public function show ($project_status, $request): Response
    {
        $this->generateLog($request->user(), "viewed this project status ({$project_status->id}).");

        return response([
            'record' => $project_status
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($project_status, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $project_status->update([
            'name'      => $request->name,
        ]);

        $this->generateLog($request->user(), "updated this project status ({$project_status->id}).");

        return response([
            'record' => $project_status
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($project_status, $request): Response
    {
        $project_status->delete();
        $this->generateLog($request->user(), "deleted this project status ({$project_status->id}).");

        return response([
            'record' => 'Project Status deleted'
        ]);
    }
}
