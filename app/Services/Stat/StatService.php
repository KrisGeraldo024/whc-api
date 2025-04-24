<?php

namespace App\Services\Stat;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Stat;
use App\Traits\GlobalTrait;

class StatService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * StatService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = Stat::orderBy('order')
        ->when($request->filled('all') , function ($query) {
            return $query->get();
        }, function ($query) {
            return $query->paginate(20);
        });

        return response([
            'records' => $records
        ]);
    }

    /**
     * StatService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'required',
            'value' => 'required',
            'order'    => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = Stat::create([
            'title'    => $request->title,
            'value' => $request->value,
            'order'    => $request->order
        ]);

        $this->generateLog($request->user(), "added this stat ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * StatService show
     * @param  Stat $stat
     * @param  Request $request
     * @return Response
     */
    public function show ($stat, $request): Response
    {
        $this->generateLog($request->user(), "viewed this stat ({$stat->id}).");
        
        return response([
            'record' => $stat
        ]);
    }

    /**
     * StatService update
     * @param  Stat $stat
     * @param  Request $request
     * @return Response
     */
    public function update ($stat, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'required',
            'value' => 'required',
            'order'    => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $stat->update([
            'title'    => $request->title,
            'value' => $request->value,
            'order'    => $request->order
        ]);

        $this->generateLog($request->user(), "updated this stat ({$stat->id}).");

        return response([
            'record' => $stat
        ]);
    }

    /**
     * StatService destroy
     * @param  Stat $stat
     * @param  Request $request
     * @return Response
     */
    public function destroy ($stat, $request): Response
    {
        $stat->delete();
        $this->generateLog($request->user(), "deleted this stat ({$stat->id}).");

        return response([
            'record' => 'Stat deleted'
        ]);
    }
}
