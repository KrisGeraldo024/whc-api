<?php

namespace App\Services\Variation;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Hash,
    Validator
};
use App\Models\{
    Accessories,
    Variation
};
use App\Traits\GlobalTrait;

class VariationService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * VariationService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = Variation::orderBy('sequence')
        ->when(isset($request->product_id), function ($query) use ($request) {
            $query->where('product_id', $request->product_id);
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
     * VariationService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'product_id'=> 'required',
            'type'      => 'required',
            'name'      => 'required',
            'price'     => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'sequence'  => 'required|integer',
            'enabled'   => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $product = null;
        
        switch ($request->type) {
            case 'accessories':
                $product = Accessories::where('id', $request->product_id)->first();
                break;
        }

        $record = Variation::create([
            'product_id'    => $product->id,
            'product_type'  => $request->type,
            'name'          => $request->name,
            'price'         => $request->price,
            'sequence'      => $request->sequence,
            'enabled'       => $request->enabled,
        ]);

        $this->generateLog($request->user(), "added this variation ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * VariationService show
     * @param  Variation $variation
     * @param  Request $request
     * @return Response
     */
    public function show ($variation, $request): Response
    {
        $this->generateLog($request->user(), "viewed this variation ({$variation->id}).");

        return response([
            'record' => $variation
        ]);
    }

    /**
     * VariationService update
     * @param  Variation $variation
     * @param  Request $request
     * @return Response
     */
    public function update ($variation, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'product_id'=> 'required',
            'type'      => 'required',
            'name'      => 'required',
            'price'     => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'sequence'  => 'required|integer',
            'enabled'   => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $product = null;
        
        switch ($request->type) {
            case 'accessories':
                $product = Accessories::where('id', $request->product_id)->first();
                break;
        }

        $variation->update([
            'product_id'    => $product->id,
            'product_type'  => $request->type,
            'name'          => $request->name,
            'price'         => $request->price,
            'sequence'      => $request->sequence,
            'enabled'       => $request->enabled,
        ]);

        $this->generateLog($request->user(), "updated this variation ({$variation->id}).");

        return response([
            'record' => $variation
        ]);
    }

    /**
     * VariationService destroy
     * @param  Variation $variation
     * @param  Request $request
     * @return Response
     */
    public function destroy ($variation, $request): Response
    {
        $this->generateLog($request->user(), "deleted this variation ({$variation->id}).");

        $variation->delete();

        return response([
            'record' => 'Variation deleted successfully!'
        ]);
    }
}
