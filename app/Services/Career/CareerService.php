<?php

namespace App\Services\Career;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Career;
use App\Traits\GlobalTrait;
use Carbon\Carbon;

class CareerService
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
    $query = Career::orderBy(isset($request->sortBy) ? $request->sortBy : 'date', isset($request->sortDirection) ? $request->sortDirection : 'desc')
    ->orderBy('order')
    ->where('job_type', ($request->type  === 'job-listings' ? 'Job Listings' : 'In-house sales group'))
    ->when(isset($request->keyword), function ($query) use ($request) {
        $query->where('title', 'LIKE', '%' . strtolower($request->keyword).'%')
        ->orWhereHas('employment_type', function ($query) use ($request) {
            $query->where('name', 'LIKE', '%' . strtolower($request->keyword).'%');
        })	
        ->orWhereHas('location', function ($query) use ($request) {
            $query->where('name', 'LIKE', '%' . strtolower($request->keyword).'%');
        });	
    })
    ->when(isset($request->location), function ($query) use ($request) {
        $query->whereHas('location', function ($q) use ($request){
            $q->where('name', $request->location);
        });
    })
    ->with('location', 'employment_type', 'images');
    $records = $request->filled('all') ? $query->get() : $query->paginate(10);
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
        $carbonDate = Carbon::now();
        $record = Career::create([
            'title'           => $request->title,
            'slug'            => str_slug($request->title),
            'description'     => $request->description,
            'qualifications'   => $request->qualification,
            'date'            => $carbonDate->format('Y-m-d H:i:s'),
            'location_id'     => $request->location_id ?? '',
            'employment_type_id'   => $request->employment_type_id ?? '',
            'job_type'          => $request->job_type  === 'job-listings' ? 'Job Listings' : 'In-house sales group',
            'order'             => Career::where('job_type',$request->job_type  === 'job-listings' ? 'Job Listings' : 'In-house sales group' )->count() + 1,
            'enabled'           => $request->enabled
        ]);

        $this->metatags($record, $request);


        $this->generateLog($request->user(), "Created", "Careers", $record);
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
    public function show ($career, $request): Response
    {
        $career->load(['metadata', 'location', 'employment_type']);
        $career->locations = json_decode($career->locations);
        
        // $this->generateLog($request->user(), "viewed this career ({$career->id}).");
        return response([
            'record' => $career
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($career, $request): Response
    {
        $carbonDate = Carbon::now();
        $career->update([
            'title'           => $request->title,
            'slug'            => str_slug($request->title),
            'description'     => $request->description,
            'qualifications'   => $request->qualification,
            'location_id'     => $request->location_id ?? '',
            'employment_type_id'   => $request->employment_type_id ?? '',
            'job_type'          => $request->job_type  === 'job-listings' ? 'Job Listings' : 'In-house sales group',
            'order'             => $career->order,
            'enabled'           => $request->enabled
        ]);

        $this->metatags($career, $request);
        $this->generateLog($request->user(), "Changed", "Careers", $career);
        return response([
            'record' => $career
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($career, $request): Response
    {
        if ($career->order !== Career::max('order')) {
            Career::where('order', '>', $career->order)->decrement('order'); 
        }
        $this->generateLog($request->user(), "Deleted", "Careers", $career);
        $career->delete();
        $this->reassignOrderValues('Career', null, $career->job_type);
        return response([
            'record' => 'Career deleted'
        ]);
    }

    public function getCareers ($request) : Response
    {
        $data = Career::select('id', 'title', 'slug', 'job_type', 'date', 'order', 'location_id', 'employment_type_id', 'enabled')
        ->orderBy(isset($request->sortBy) ? $request->sortBy : 'date', isset($request->sortDirection) ? $request->sortDirection : 'desc')
        ->orderBy('order')
        ->where('enabled', 1)
        ->where('job_type', $request->type)
        ->with(['metadata', 'location', 'employment_type'])
        ->paginate(12);

    return response ([
            'record' => $data
        ]);
    }

    public function getCareer ($request) : Response
    {
        $data = Career::where('slug', $request->slug)->with(['metadata', 'location', 'employment_type'])->first();

    return response ([
            'record' => $data
        ]);
    }
}
