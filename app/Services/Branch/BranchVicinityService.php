<?php

namespace App\Services\Branch;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\BranchVicinity;
use App\Traits\GlobalTrait;

class BranchVicinityService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * BranchVicinityService index
     * @param  BranchRegion $region
     * @param  Request $request
     * @return Response
     */
    public function index ($region, $request): Response
    {
        $records = BranchVicinity::orderBy('order')
        ->whereBranchRegionId($region->id)
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
     * BranchVicinityService store
     * @param  BranchRegion $region
     * @param  Request $request
     * @return Response
     */
    public function store ($region, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required',
            'order'   => 'required|integer',
            'enabled' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = BranchVicinity::create([
            'branch_region_id' => $region->id,
            'title'            => $request->title,
            'order'            => $request->order,
            'enabled'          => $request->enabled,
            'slug'             => $this->slugify($request->title, 'BranchVicinity')
        ]);

        $this->generateLog($request->user(), "added this branch vicinity ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * BranchVicinityService show
     * @param  BranchRegion $region
     * @param  BranchVicinity $vicinity
     * @param  Request $request
     * @return Response
     */
    public function show ($region, $vicinity, $request): Response
    {
        $vicinity->load('branchRegion');
        $this->generateLog($request->user(), "viewed this branch vicinity ({$vicinity->id}).");
        
        return response([
            'record' => $vicinity
        ]);
    }

    /**
     * BranchVicinityService update
     * @param  BranchRegion $region
     * @param  BranchVicinity $vicinity
     * @param  Request $request
     * @return Response
     */
    public function update ($region, $vicinity, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required',
            'order'   => 'required|integer',
            'enabled' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $vicinity->update([
            'branch_region_id' => $region->id,
            'title'            => $request->title,
            'order'            => $request->order,
            'enabled'          => $request->enabled,
            'slug'             => $this->slugify($request->title, 'BranchVicinity', $vicinity->id)
        ]);

        $this->generateLog($request->user(), "updated this branch vicinity ({$vicinity->id}).");

        return response([
            'record' => $vicinity
        ]);
    }

    /**
     * BranchVicinityService destroy
     * @param  BranchRegion $region
     * @param  BranchVicinity $vicinity
     * @param  Request $request
     * @return Response
     */
    public function destroy ($region, $vicinity, $request): Response
    {
        $vicinity->delete();
        $this->generateLog($request->user(), "deleted this branch vicinity ({$vicinity->id}).");

        return response([
            'record' => 'Branch vicinity deleted'
        ]);
    }
}
