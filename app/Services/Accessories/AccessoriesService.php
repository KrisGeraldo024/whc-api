<?php

namespace App\Services\Accessories;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Hash,
    Validator
};
use App\Models\{
    Accessories,
    Discount,
    DiscountProduct,
    HearingAid,
};
use App\Traits\GlobalTrait;

class AccessoriesService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * AccessoriesService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = Accessories::orderBy('title')
        ->when(isset($request->keyword), function ($query) use ($request) {
            $query->where('title', 'LIKE', '%' . strtolower($request->keyword).'%');	
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
     * AccessoriesService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'             => 'required',
            'description'       => 'required',
            'price'             => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'featured'          => 'required',
            'enabled'           => 'required',
            'discount_id'       => 'sometimes',
            'compatible_hearing_aid'      => 'sometimes',
            'thumbnail_image'   => 'required',
            'thumbnail_image.*' => 'required|mimes:svg,png,webp|max:3000',
            'button_label'      => 'sometimes'  
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $keyword = sprintf('%s,%s,%s',
            $request->title,
            str_slug($request->title),
            str_slug($request->title, '_')
        );

        $record = Accessories::create([
            'title'       => $request->title,
            'slug'        => $this->slugify($request->title, 'Accessories'),
            'description' => $request->description,
            'keyword'     => $keyword,
            'price'       => $request->price,
            'featured'    => $request->featured,
            'enabled'     => $request->enabled,
            'discount_id' => $request->discount_id,
            'compatible_hearing_aid'=> $request->compatible_hearing_aid,
            'button_label'=> $request->button_label
        ]);

        if (isset($request->discount_id)) {
            $discount = Discount::where('id', $request->discount_id)
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('end_date', '>=', date('Y-m-d'))
            ->where('enabled', 1)
            ->where('expired', 0)
            ->first();

            if ($discount) {
                DiscountProduct::create([
                    'discount_id' => $discount->id,
                    'product_id' => $record->id,
                    'product_type' => 'accessories',
                ]);
            }
        }

        if ($request->hasFile('thumbnail_image')) {
            $this->addImages('accessories', $request, $record, 'thumbnail_image');
        }

        $this->metatags($record, $request);
        $this->generateLog($request->user(), "added this accessories ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * AccessoriesService show
     * @param  Accessories $accessory
     * @param  Request $request
     * @return Response
     */
    public function show ($accessory, $request): Response
    {
        $this->generateLog($request->user(), "viewed this accessories ({$accessory->id}).");

        $accessory->load(['images', 'metadata', 'discount.discount']);

        $accessory->compatible_hearing_aid = ($accessory->compatible_hearing_aid && gettype($accessory->compatible_hearing_aid) == 'string') ? HearingAid::whereIn('id', json_decode($accessory->compatible_hearing_aid))
        ->get() : [];
    

        return response([
            'record' => $accessory
        ]);
    }

    /**
     * AccessoriesService update
     * @param  Accessories $accessory
     * @param  Request $request
     * @return Response
     */
    public function update ($accessory, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'             => 'required',
            'description'       => 'required',
            'price'             => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'compatible_hearing_aid'      => 'sometimes',
            'featured'          => 'required',
            'enabled'           => 'required',
            'discount_id'           => 'sometimes',
            'thumbnail_image.*' => 'sometimes|mimes:svg,png,webp|max:3000',
            'button_label'      => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $keyword = sprintf('%s,%s,%s',
            $request->title,
            str_slug($request->title),
            str_slug($request->title, '_')
        );

        $accessory->update([
            'title'       => $request->title,
            'slug'        => $this->slugify($request->title, 'Accessories', $accessory->id),
            'description' => $request->description,
            'keyword'     => $keyword,
            'price'       => $request->price,
            'compatible_hearing_aid'       => $request->compatible_hearing_aid,
            'discount_id'    => $request->discount_id,
            'featured'    => $request->featured,
            'enabled'     => $request->enabled,
            'button_label'=> $request->button_label
        ]);

        if (isset($request->discount_id)) {
            $discount = Discount::where('id', $request->discount_id)
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('end_date', '>=', date('Y-m-d'))
            ->where('enabled', 1)
            ->where('expired', 0)
            ->first();

            if ($discount) {
                // DiscountProduct::updateOrCreate(
                //     [
                //         'discount_id' => $discount->id,
                //         'product_id' => $accessory->id,
                //     ],
                //     [
                //         'product_type' => 'accessories',
                //     ]
                // );
                //check if product has already discount
                $existing_discount_product = DiscountProduct::where('product_id', $accessory->id)
                ->first();

                if ($existing_discount_product) {
                    $existing_discount_product->update([
                        'discount_id' => $discount->id
                    ]);
                }
                else {
                    DiscountProduct::create([
                        'discount_id' => $discount->id,
                        'product_id' => $accessory->id,
                        'product_type' => 'accessories',
                    ]);
                }
            }
            else {
                $accessory->discount()->delete();
            }
        }
        else {
            $accessory->discount()->delete();
        }

        $this->metatags($accessory, $request);
        $this->updateImages('accessories', $request, $accessory, 'thumbnail_image');
        $this->generateLog($request->user(), "updated this accessories ({$accessory->id}).");

        return response([
            'record' => $accessory
        ]);
    }

    /**
     * AccessoriesService destroy
     * @param  Accessories $accessory
     * @param  Request $request
     * @return Response
     */
    public function destroy ($accessory, $request): Response
    {
        $this->generateLog($request->user(), "deleted this accessories ({$accessory->id}).");

        $accessory->delete();

        return response([
            'record' => 'Accessories deleted successfully!'
        ]);
    }
}
