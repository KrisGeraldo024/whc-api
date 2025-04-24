<?php

namespace App\Services\PageTemplate;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\PageTemplate;
use App\Traits\GlobalTrait;

class PageTemplateService
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
        
        $records = PageTemplate::orderBy('sequence')
        ->when(isset($request->enabled), function ($query) use ($request) {
            $query->where('enabled',strtolower($request->enabled));	
        })
        ->when($request->filled('all') , function ($query, $request) {
            return $query->get();
        }, function ($query) {
            return $query->paginate(20);
        });


        // $records = PageTemplate::orderBy('sequence')
        // ->when($request->filled('all') , function ($query) {
        //     return $query->get();
        // }, function ($query) {
        //     return $query->paginate(20);
        // });

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

        $record = PageTemplate::create([
            'title'        => $request->title? $request->title:'',
            'sequence'        => $request->sequence? $request->sequence:'0',
            'services'        => $request->services? $request->services:'0',
            'enabled'      => $request->enabled? $request->enabled:'0',
            'information'     => $request->information? $request->information:'0',
            'remittance'     => $request->remittance? $request->remittance:'0',
            'travel'        => $request->travel? $request->travel:'0',
            'currency'      => $request->currency? $request->currency:'0',
            'telcos'     => $request->telcos? $request->telcos:'0',
            'information_type'     => $request->information_type? $request->information_type:'0',
            'remittance_type'     => $request->remittance_type? $request->remittance_type:'0',
            'travel_type'     => $request->travel_type? $request->travel_type:'0',
            'currency_type'     => $request->currency_type? $request->currency_type:'0',
            'telcos_type'     => $request->telcos_type? $request->telcos_type:'0',

        ]); 
        
        $this->generateLog($request->user(), "added this property ({$record->id}).");

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
    public function show ($request,$pageTemplate): Response
    {

        $this->generateLog($request->user(), "viewed this page template ({$pageTemplate->id}).");

        return response([
            'record' => $pageTemplate
        ]);
    }


    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($request,$pageTemplate): Response
    {
        $pageTemplate->update([
            'title'        => $request->title,
            'sequence'        => $request->sequence,
            'services'        => $request->services,
            'enabled'      => $request->enabled,
            'information'     => $request->information,
            'remittance'     => $request->remittance,
            'travel'        => $request->travel,
            'currency'      => $request->currency,
            'telcos'     => $request->telcos,
            'information_type'     => $request->information_type,
            'remittance_type'     => $request->remittance_type,
            'travel_type'     => $request->travel_type,
            'currency_type'     => $request->currency_type,
            'telcos_type'     => $request->telcos_type,
        ]);

        $this->generateLog($request->user(), "updated this pageTemplate ({$pageTemplate->id}).");

        return response([
            'record' => $pageTemplate
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($request,$pageTemplate): Response
    {
        $pageTemplate->delete();
        $this->generateLog($request->user(), "deleted this page template ({$pageTemplate->id}).");

        return response([
            'record' => 'Page Template deleted'
        ]);
    }
}
