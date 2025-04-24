<?php

namespace App\Services\Office;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Office;
use App\Traits\GlobalTrait;

class OfficeService
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
        $records = Office::orderBy('order')
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
     * FaqService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
       
        $record = Office::create([
            'office_name'   => $request->office_name,
            'address'       => $request->address,
            'order'         => $request->order,
            'telephone'     => $request->telephone,
            'hotline'       => $request->hotline,
            'link_address'  => $this->extractMapCode($request->link_address),
            'enabled'       => $request->enabled,
            'emails'       => $request->emails,


        ]);

        $this->generateLog($request->user(), "added this office ({$record->id}).");

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
    public function show ($office, $request): Response
    {

        $office->emails = json_decode($office->emails);
        
        $this->generateLog($request->user(), "viewed this office ({$office->id}).");



        return response([
            'record' => $office
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($office, $request): Response
    {
       

        $office->update([
            'office_name'   => $request->office_name,
            'address'       => $request->address,
            'order'         => $request->order,
            'telephone'     => $request->telephone,
            'hotline'       => $request->hotline,
            'link_address'  => $this->extractMapCode($request->link_address),
            'enabled'       => $request->enabled,
            'emails'        => $request->emails,
        ]);

        $this->generateLog($request->user(), "updated this office ({$office->id}).");

        return response([
            'record' => $office
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($office, $request): Response
    {
        $office->delete();
        $this->generateLog($request->user(), "deleted this faq ({$office->id}).");

        return response([
            'record' => 'Office deleted'
        ]);
    }
}
