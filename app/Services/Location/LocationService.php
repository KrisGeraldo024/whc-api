<?php

namespace App\Services\Location;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Location;
use App\Traits\GlobalTrait;

class LocationService
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
        $records = Location::when(isset($request->keyword), function ($query) use ($request) {
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

        $record = Location::create([
            'name'    => $request->name,
        ]);

        $this->generateLog($request->user(), "added this location ({$record->id}).");

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
    public function show ($location, $request): Response
    {
        $this->generateLog($request->user(), "viewed this location ({$location->id}).");

        return response([
            'record' => $location
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($location, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $location->update([
            'name'      => $request->name,
        ]);

        $this->generateLog($request->user(), "updated this location ({$location->id}).");

        return response([
            'record' => $location
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($location, $request): Response
    {
        $location->delete();
        $this->generateLog($request->user(), "deleted this location ({$location->id}).");

        return response([
            'record' => 'Location deleted'
        ]);
    }
}
