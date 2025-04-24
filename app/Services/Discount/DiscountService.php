<?php

namespace App\Services\Discount;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Hash,
    Validator
};
use App\Models\Discount;
use App\Traits\GlobalTrait;

class DiscountService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * DiscountService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = Discount::orderByDesc('end_date')
        ->when(isset($request->keyword), function ($query) use ($request) {
            $query->where('title', 'LIKE', '%'.strtolower($request->keyword).'%');	
        })
        ->when(isset($request->expired), function ($query) use ($request) {
            $query->where('expired', $request->expired);
        })
        ->when(isset($request->enabled), function ($query) use ($request) {
            $query->where('enabled', $request->enabled);
        })
        ->when(isset($request->active), function ($query) use ($request) {
            $query->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('end_date', '>=', date('Y-m-d'));
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
     * DiscountService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'             => 'required',
            'start_date'        => 'required|date_format:Y-m-d',
            'end_date'          => 'required|date_format:Y-m-d||after_or_equal:start_date',
            'type'              => 'required|in:percent,flat-rate',
            'amount'            => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'enabled'           => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = Discount::create([
            'title'         => $request->title,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'type'          => $request->type,
            'amount'       => $request->amount,
            'enabled'       => $request->enabled,
        ]);

        $this->generateLog($request->user(), "added this discount ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * DiscountService show
     * @param  Discount $discount
     * @param  Request $request
     * @return Response
     */
    public function show ($discount, $request): Response
    {
        $this->generateLog($request->user(), "viewed this discount ({$discount->id}).");

        if ($discount->end_date < date('Y-m-d')) {
            $discount->update([
                'expired' => 1
            ]);
        }

        return response([
            'record' => $discount
        ]);
    }

    /**
     * DiscountService update
     * @param  Discount $discount
     * @param  Request $request
     * @return Response
     */
    public function update ($discount, $request): Response
    {
        if ($discount->end_date < date('Y-m-d')) {
            $discount->update([
                'expired' => 1
            ]);
        }

        $validator = Validator::make($request->all(), [
            'title'             => 'required',
            'start_date'        => 'required|date_format:Y-m-d',
            'end_date'          => 'required|date_format:Y-m-d||after_or_equal:start_date',
            'type'              => 'required|in:percent,flat-rate',
            'amount'           => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'enabled'           => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        if ($discount->expired) {
            return response([
                'errors' => ['Discount expired!']
            ], 400);
        }

        $discount->update([
            'title'         => $request->title,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'type'          => $request->type,
            'amount'       => $request->amount,
            'enabled'       => $request->enabled,
        ]);

        $this->generateLog($request->user(), "updated this discount ({$discount->id}).");

        return response([
            'record' => $discount
        ]);
    }

    /**
     * DiscountService destroy
     * @param  Discount $discount
     * @param  Request $request
     * @return Response
     */
    public function destroy ($discount, $request): Response
    {
        $this->generateLog($request->user(), "deleted this discount ({$discount->id}).");

        $discount->delete();

        return response([
            'record' => 'Discount deleted successfully!'
        ]);
    }
}
