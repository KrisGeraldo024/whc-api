<?php

namespace App\Services\BusinessUnit;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\BusinessUnit;
use App\Traits\GlobalTrait;

class BusinessUnitService
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
        $records = BusinessUnit::orderBy('order')->when(isset($request->keyword), function ($query) use ($request) {
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
            'name'          => 'required',
            'order'         => 'required',
            'description_1' => 'required',
            'description_2' => 'required',
            'tagline'       => 'required',
            'about'         => 'required',
            'slug'          => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = BusinessUnit::create([
            'name'          => $request->name,
            'order'         => $request->order,
            'description_1' => $request->description_1,
            'description_2' => $request->description_2,
            'tagline'       => $request->tagline,
            'about'         => $request->about,
            'website_url'   => $request->website_url,
            'slug'          => $request->slug
        ]);

        $this->addImages('business_unit', $request, $record, 'main_image');
        $this->addImages('business_unit', $request, $record, 'about_image');
        $this->addImages('business_unit', $request, $record, 'icon');

        $this->generateLog($request->user(), "added this business_unit ({$record->id}).");

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
    public function show ($business_unit, $request): Response
    {
        $this->generateLog($request->user(), "viewed this business_unit ({$business_unit->id}).");

        $business_unit->load('images');

        return response([
            'record' => $business_unit
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($business_unit, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'order'         => 'required',
            'description_1' => 'required',
            'description_2' => 'required',
            'tagline'       => 'required',
            'about'         => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $business_unit->update([
            'name'          => $request->name,
            'order'         => $request->order,
            'description_1' => $request->description_1,
            'description_2' => $request->description_2,
            'tagline'       => $request->tagline,
            'about'         => $request->about,
            'website_url'   => $request->website_url
        ]);

        $this->generateLog($request->user(), "updated this business_unit ({$business_unit->id}).");

        return response([
            'record' => $business_unit
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($business_unit, $request): Response
    {
        $business_unit->delete();
        $this->generateLog($request->user(), "deleted this business_unit ({$business_unit->id}).");

        return response([
            'record' => 'BusinessUnit deleted'
        ]);
    }
}
