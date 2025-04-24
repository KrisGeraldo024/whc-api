<?php

namespace App\Services\Property;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Vicinity;
use App\Models\Property;
use App\Models\Location;
use App\Models\Amenity;

use App\Traits\GlobalTrait;

class AmenityService
{
    use GlobalTrait;
    
    public function index ($request): Response
    {
        $records = Amenity::orderBy(isset($request->sortBy) ? $request->sortBy : 'order', isset($request->sortDirection) ? $request->sortDirection : 'desc')
        ->where('parent_id', $request->property)
        // ->where('property_type', ($request->unitType  === 'house-and-lots' ? 'House & Lot' : 'Condominium'))
        ->when(isset($request->keyword), function ($query) use ($request) {
            $query->where('content', 'LIKE', '%' . strtolower($request->keyword).'%');	
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
                    if (Amenity::where('parent_id', $request->parent_id)->whereNull('deleted_at')->where('content', $value)->count() >= 1) {
                        $fail('The title has already been taken.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 402);
        }
        $record = Amenity::create([
           
            'content'          => $request->title,
            'order'            => $request->order ?? Amenity::where('parent_id', $request->parent_id)->count() + 1,
            'parent_id'       => $request->parent_id,
            'enabled'           => $request->enabled
           
        ]);


        $this->generateLog($request->user(), "Created", "Amenity", $record);

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
        // $amenity->load('images');

        // $this->generateLog($request->user(), "viewed this amenity ({$amenity->id}).");

        return response([
            'record' => $amenity
        ]);
    }

    /**
     * FaqService update
     * @param  Request $request
     * @return Response
     */
    public function update ($amenity, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'  => [
                function ($attribute, $value, $fail) use ($amenity) {
                    if ($value !== $amenity->content && Amenity::where('content', $value)->count() > 1) {
                        $fail('The title has already been taken.');
                    }
                },
            ]
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 402);
        }
        $amenity->update([
            'content'          => $request->title,
            'order'            => $request->order ?? $amenity->order,
            'parent_id'        => $request->parent_id,
            'enabled'          => $request->enabled
        ]);


        // $this->updateImages('amenity', $request, $amenity, 'main_image');

        $this->generateLog($request->user(), "Changed", "Amenity", $amenity);

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
        
        if ($amenity->order !== Amenity::max('order')) {
            Amenity::where('order', '>', $amenity->order)->decrement('order'); 
        }
        $this->generateLog($request->user(), "Deleted", "Amenity", $amenity);
        $amenity->delete();

        $this->reassignOrderValues('Amenity', $amenity->parent_id);
        return response([
            'record' => 'Project deleted'
        ]);
    }
}
