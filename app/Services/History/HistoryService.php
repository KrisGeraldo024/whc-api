<?php

namespace App\Services\History;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\History;
use App\Traits\GlobalTrait;

class HistoryService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * HistoryService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = History::orderBy('order')
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
     * HistoryService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
       

        $record = History::create([
            'title'    => $request->title,
            'subtitle' => $request->subtitle,
            'year'     => $request->year,
            'order'    => $request->order
        ]);

        if ($request->hasFile('main_image')) {
            $this->addImages('history', $request, $record, 'main_image');
        }

        $this->generateLog($request->user(), "added this history ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * HistoryService show
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function show ($history, $request): Response
    {
        $history->load('images');
        $this->generateLog($request->user(), "viewed this history ({$history->id}).");
        
        return response([
            'record' => $history
        ]);
    }

    /**
     * HistoryService update
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function update ($history, $request): Response
    {
       
        $history->update([
            'title'    => $request->title,
            'subtitle' => $request->subtitle,
            'year'     => $request->year,
            'order'    => $request->order
        ]);

        if ($request->hasFile('main_image')) {
            $this->updateImages('history', $request, $history, 'main_image');
        }
        $this->generateLog($request->user(), "updated this history ({$history->id}).");

        return response([
            'record' => $history
        ]);
    }

    /**
     * HistoryService destroy
     * @param  History $history
     * @param  Request $request
     * @return Response
     */
    public function destroy ($history, $request): Response
    {
        $history->delete();
        $this->generateLog($request->user(), "deleted this history ({$history->id}).");

        return response([
            'record' => 'History deleted'
        ]);
    }
}
