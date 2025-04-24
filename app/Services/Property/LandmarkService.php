<?php

namespace App\Services\Property;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Vicinity;
use App\Models\Property;
use App\Models\Location;
use App\Models\Landmark;

use App\Traits\GlobalTrait;

class LandmarkService
{
    use GlobalTrait;
    
    public function index ($request): Response
    {
        $records = Landmark::orderBy(isset($request->sortBy) ? $request->sortBy : 'created_at', isset($request->sortDirection) ? $request->sortDirection : 'desc')
        ->where('parent_id', $request->property)
        // ->where('property_type', ($request->unitType  === 'house-and-lots' ? 'House & Lot' : 'Condominium'))
        ->when(isset($request->keyword), function ($query) use ($request) {
            $query->where('name', 'LIKE', '%' . strtolower($request->keyword).'%');	
        })
        ->when($request->filled('all') , function ($query, $request) {
            return $query->get();
        }, function ($query) {
            return $query->paginate(10);
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
            'title'               =>  [
                function ($attribute, $value, $fail) use ($request){
                    if (Landmark::where('parent_id', $request->parent_id)->whereNull('deleted_at')->where('name', $value)->count() >= 1) {
                        $fail('The name has already been taken.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 402);
        }
        $record = Landmark::create([
           
            'name'          => $request->title,
            'order'            => $request->order ?? Landmark::where('parent_id', $request->parent_id)->count() + 1,
            'parent_id'       => $request->parent_id,
            // 'enabled'           => $request->enabled
           
        ]);

        foreach($request->location_name as $index => $location) {
            $loc = Vicinity::create([
                'parent_id' => $record->id,
                'content' => $request->location_name[$index],
                'order' => $request->location_order[$index],
                'type'  => $record->name,
                'distance' => $request->distance[$index]
            ]);
        }

        $this->addImages('landmark', $request, $record, 'icon');


        $this->generateLog($request->user(), "Created", "Landmark", $record);

        return response([
            'record' => $record
        ]);
    }

    /**
     * FaqService show
     * @param  Request $request
     * @return Response
     */
    public function show ($landmark, $request): Response
    {
        //$testimonial->load('images');
        $landmark->load(['images', 'vicinities' => function ($q) {
            $q->orderBy('order');
        }]);

        // $this->generateLog($request->user(), "viewed this landmark ({$landmark->id}).");

        return response([
            'record' => $landmark
        ]);
    }

    /**
     * FaqService update
     * @param  Request $request
     * @return Response
     */
    public function update ($landmark, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'  => [
                function ($attribute, $value, $fail) use ($landmark) {
                    if ($value !== $landmark->name && Landmark::where('name', $value)->count() > 1) {
                        $fail('The name has already been taken.');
                    }
                },
            ]
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 402);
        }
        $landmark->update([
            'name'          => $request->title,
            'order'            => $request->order ?? $landmark->order,
            'parent_id'       => $request->parent_id,
        ]);

        foreach($request->location_name as $index => $location) {
            if($request->location_id[$index]) {
                $loc = Vicinity::find($request->location_id[$index]);

                $loc->update([
                   'parent_id' => $landmark->id,
                    'content' => $request->location_name[$index],
                    'order' => $request->location_order[$index],
                    'type'  => $landmark->name,
                    'distance' => $request->distance[$index]
                ]);
            } else {
                $loc = Vicinity::create([
                    'parent_id' => $landmark->id,
                    'content' => $request->location_name[$index],
                    'order' => $request->location_order[$index],
                    'type'  => $landmark->name,
                    'distance' => $request->distance[$index]
                ]);
            }
        }

        if($request->has('icon')) {
            $this->updateImages('landmark', $request, $landmark, 'icon');
        }

        $landmark->load(['images', 'vicinities' => function ($q) {
            $q->orderBy('order');
        }]);

        $this->generateLog($request->user(), "Changed", "Landmark", $landmark);

        return response([
            'record' => $landmark
        ]);
    }

    /**
     * FaqService destroy
     * @param  Request $request
     * @return Response
     */
    public function destroy ($landmark, $request): Response
    {
        if ($landmark->order !== Landmark::max('order')) {
            Landmark::where('order', '>', $landmark->order)->decrement('order'); 
        }
        $this->generateLog($request->user(), "Deleted", "Landmark", $landmark);
        $landmark->delete();

        $this->reassignOrderValues('Landmark', $landmark->parent_id);
        return response([
            'record' => 'Project deleted'
        ]);
    }
}
