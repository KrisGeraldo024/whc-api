<?php

namespace App\Services\Property;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\PropertySubcategory;
use App\Traits\GlobalTrait;

class PropertySubcategoryService
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
        $records = PropertySubcategory::when(isset($request->keyword), function ($query) use ($request) {
            $query->where('content', 'LIKE', '%' . strtolower($request->keyword).'%');	
        })
        ->with('property')
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
        $record = PropertySubcategory::create([
            'title'        => $request->title,
            'slug'         => str_slug($request->title),
            'order'        => $request->order,
            'enabled'      => $request->enabled,
            'featured'     => $request->featured,
            'property_id'   => $request->property_id,
            'tagline'        => $request->tagline,
            
        ]);

        $this->addImages('property_subcategory', $request, $record, 'main_image');


        if ($request->hasFile('promo_banner')) {
            $this->addImages('property', $request, $record, 'promo_banner');
        }

        $this->generateLog($request->user(), "added this property subcategory ({$record->id}).");

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
    public function show ($property_subcategory, $request): Response
    {
        $property_subcategory->load('images');

        $this->generateLog($request->user(), "viewed this project status ({$property_subcategory->id}).");

        return response([
            'record' => $property_subcategory
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($property_subcategory, $request): Response
    {
        $property_subcategory->update([
            'title'        => $request->title,
            'slug'         => str_slug($request->title),
            'order'        => $request->order,
            'enabled'      => $request->enabled,
            'featured'     => $request->featured,
            'property_id'   => $request->property_id,
            'tagline'        => $request->tagline,
        ]);

        $this->updateImages('property_subcategory', $request, $property_subcategory, 'main_image');
        
        if ($request->hasFile('promo_banner')) {
            $this->updateImages('property', $request, $property, 'promo_banner');
        }

        $this->generateLog($request->user(), "updated this property subcategory ({$property_subcategory->id}).");

        return response([
            'record' => $property_subcategory
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($property_subcategory, $request): Response
    {
        $property_subcategory->delete();
        $this->generateLog($request->user(), "deleted this project status ({$property_subcategory->id}).");

        return response([
            'record' => 'Project Status deleted'
        ]);
    }
}
