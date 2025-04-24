<?php

namespace App\Services\Branch;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\BranchRegion;
use App\Traits\GlobalTrait;

class BranchRegionService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * BranchRegionService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = BranchRegion::orderBy('order')
        ->with('vicinities')
        ->when($request->filled('all') , function ($query) {
            return $query->get();
        }, function ($query) {
            return $query->paginate(20);
        });

        return response([
            'records' => $records
        ]);
    }

    /**
     * BranchRegionService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'order' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = BranchRegion::create([
            'title' => $request->title,
            'order' => $request->order,
            'slug'  => $this->slugify($request->title, 'BranchRegion')
        ]);

        $this->generateLog($request->user(), "added this branch region ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * BranchRegionService show
     * @param  BranchRegion $region
     * @param  Request $request
     * @return Response
     */
    public function show ($region, $request): Response
    {
        $this->generateLog($request->user(), "viewed this branch region ({$region->id}).");
        
        return response([
            'record' => $region
        ]);
    }

    /**
     * BranchRegionService update
     * @param  BranchRegion $region
     * @param  Request $request
     * @return Response
     */
    public function update ($region, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'order' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $region->update([
            'title' => $request->title,
            'order' => $request->order,
            'slug'  => $this->slugify($request->title, 'BranchRegion', $region->id)
        ]);

        $this->generateLog($request->user(), "updated this branch region ({$region->id}).");

        return response([
            'record' => $region
        ]);
    }

    /**
     * BranchRegionService destroy
     * @param  BranchRegion $region
     * @param  Request $request
     * @return Response
     */
    public function destroy ($region, $request): Response
    {
        $region->delete();
        $this->generateLog($request->user(), "deleted this branch region ({$region->id}).");

        return response([
            'record' => 'Branch region deleted'
        ]);
    }
}
