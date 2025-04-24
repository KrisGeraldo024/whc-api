<?php

namespace App\Services\Branch;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Branch;
use App\Traits\GlobalTrait;

class BranchService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * BranchService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = Branch::orderBy('order')
        ->when($request->filled('region'), function ($query) use ($request) {
            $query->whereBranchRegionId($request->region);
        })
        ->when($request->filled('vicinity'), function ($query) use ($request) {
            $query->whereBranchVicinityId($request->vicinity);
        })
        ->with(['branchRegion', 'branchVicinity', 'formDetails'])
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
     * BranchService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'branch_region_id'   => 'required',
            'branch_vicinity_id' => 'required',
            'title'              => 'required',
            'address'            => 'required',
            'google_map_embed'   => 'required',
            'timeslot'           => 'required',
            'telephone_number'   => 'required',
            'email_address'      => 'required',
            'order'              => 'required|integer',
            'upcoming'           => 'required',
            'enabled'            => 'required',
            'main_image'         => 'required',
            'main_image.*'       => 'required|mimes:jpeg,png,jpg,webp|max:3000'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = Branch::create([
            'branch_region_id'   => $request->branch_region_id,
            'branch_vicinity_id' => $request->branch_vicinity_id,
            'title'              => $request->title,
            'description'        => $request->description,
            'address'            => $request->address,
            'google_map_embed'   => $request->google_map_embed,
            'timeslot'           => $request->timeslot,
            'telephone_number'   => $request->telephone_number,
            'email_address'      => $request->email_address,
            'order'              => $request->order,
            'upcoming'           => $request->upcoming,
            'enabled'            => $request->enabled,
            'slug'               => $this->slugify($request->title, 'Branch')
        ]);

        if ($request->hasFile('main_image')) {
            $this->addImages('branch', $request, $record, 'main_image');
        }

        $this->metatags($record, $request);
        $this->generateLog($request->user(), "added this branch ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * BranchService show
     * @param  Branch $branch
     * @param  Request $request
     * @return Response
     */
    public function show ($branch, $request): Response
    {
        $branch->load('images', 'metadata');
        $this->generateLog($request->user(), "viewed this branch ({$branch->id}).");

        return response([
            'record' => $branch
        ]);
    }

    /**
     * BranchService update
     * @param  Branch $branch
     * @param  Request $request
     * @return Response
     */
    public function update ($branch, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'branch_region_id'   => 'required',
            'branch_vicinity_id' => 'required',
            'title'              => 'required',
            'address'            => 'required',
            'google_map_embed'   => 'required',
            'timeslot'           => 'required',
            'telephone_number'   => 'required',
            'email_address'      => 'required',
            'order'              => 'required|integer',
            'upcoming'           => 'required',
            'enabled'            => 'required',
            'main_image'         => 'sometimes',
            'main_image.*'       => 'sometimes|mimes:jpeg,png,jpg,webp|max:3000'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $branch->update([
            'branch_region_id'   => $request->branch_region_id,
            'branch_vicinity_id' => $request->branch_vicinity_id,
            'title'              => $request->title,
            'description'        => $request->description,
            'address'            => $request->address,
            'google_map_embed'   => $request->google_map_embed,
            'timeslot'           => $request->timeslot,
            'telephone_number'   => $request->telephone_number,
            'email_address'      => $request->email_address,
            'order'              => $request->order,
            'upcoming'           => $request->upcoming,
            'enabled'            => $request->enabled,
            'slug'               => $this->slugify($request->title, 'Branch', $branch->id)
        ]);

        $this->updateImages('branch', $request, $branch, 'main_image');

        $this->metatags($branch, $request);
        $this->generateLog($request->user(), "updated this branch ({$branch->id}).");

        return response([
            'record' => $branch
        ]);
    }

    /**
     * BranchService destroy
     * @param  Branch $branch
     * @param  Request $request
     * @return Response
     */
    public function destroy ($branch, $request): Response
    {
        $branch->delete();
        $this->generateLog($request->user(), "deleted this branch ({$branch->id}).");

        return response([
            'record' => 'Branch deleted'
        ]);
    }
}
