<?php

namespace App\Services\BusinessUnit;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\BusinessUnitsDirectory;
use App\Traits\GlobalTrait;

class BusinessUnitsDirectoryService
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
        $records = BusinessUnitsDirectory::orderBy('order')->when(isset($request->keyword), function ($query) use ($request) {
            $query->where('name', 'LIKE', '%' . strtolower($request->keyword).'%');	
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
            'category_id'   => 'required',
            'address'       => 'required',
            'contact'       => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = BusinessUnitsDirectory::create([
            'name'              => $request->name,
            'order'             => $request->order,
            'business_unit_id'  => $request->category_id,
            'address'           => $request->address,
            'contact_number'    => $request->contact,
            'gmaps_address'     => $request->gmaps_url,
            'website_url'       => $request->website_url,
            'facebook_url'      => $request->fb_link,
            'email'             => $request->email
        ]);


        $this->generateLog($request->user(), "added this business_unit_directory ({$record->id}).");

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
    public function show ($business_unit_directory, $request): Response
    {
        $this->generateLog($request->user(), "viewed this business_unit_directory ({$business_unit_directory->id}).");

        // $business_unit_directory->load('images');
        
        return response([
            'record' => $business_unit_directory
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($business_unit_directory, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'order'         => 'required',
            'category_id'   => 'required',
            'address'       => 'required',
            'contact'       => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $business_unit_directory->update([
            'name'              => $request->name,
            'order'             => $request->order,
            'business_unit_id'  => $request->category_id,
            'address'           => $request->address,
            'contact_number'    => $request->contact,
            'gmaps_address'     => $request->gmaps_url,
            'website_url'       => $request->website_url,
            'facebook_url'      => $request->fb_link,
            'email'             => $request->email
        ]);

        $this->generateLog($request->user(), "updated this business_unit_directory ({$business_unit_directory->id}).");

        return response([
            'record' => $business_unit_directory
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($business_unit_directory, $request): Response
    {
        if ($business_unit_directory->delete()) {
            $this->generateLog($request->user(), "deleted this business_unit_directory ({$business_unit_directory->id}).");

            return response([
                'record' => 'Business Unit Directory deleted'
            ]);
        } else {
            return response([
                'record' => 'Business Unit Directory not found'
            ]);
        }
    }
}
