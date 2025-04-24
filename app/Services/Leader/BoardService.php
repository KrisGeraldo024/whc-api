<?php

namespace App\Services\Leader;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Board;
use App\Traits\GlobalTrait;

class BoardService
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
        $records = Board::orderBy('order')
        ->when(isset($request->keyword), function ($query) use ($request) {
            $query->where('content', 'LIKE', '%' . strtolower($request->keyword).'%');	
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
        $record = Board::create([
            'name'      => $request->name,
            'position'  => $request->position,
            'biography' => $request->biography,
            'order'     => $request->order,
            'enabled'    => $request->enabled,

        ]);


        // $this->addImages('board', $request, $record, 'icon');
        $this->addImages('board', $request, $record, 'main_image');
        $this->metatags($record, $request);
        
        $this->generateLog($request->user(), "added this board ({$record->id}).");

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
    public function show ($board, $request): Response
    {
        $board->load('images', 'metadata');

        $this->generateLog($request->user(), "viewed this board ({$board->id}).");

        return response([
            'record' => $board
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($board, $request): Response
    {
        $board->update([
            'name'      => $request->name,
            'position'   => $request->position,
            'biography'   => $request->biography,
            'order'     => $request->order,
            'enabled'    => $request->enabled,
        ]);

        // $this->updateImages('board', $request, $board, 'icon');
        $this->updateImages('board', $request, $board, 'main_image');
        $this->metatags($board, $request);

        $this->generateLog($request->user(), "updated this board ({$board->id}).");

        return response([
            'record' => $board
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($board, $request): Response
    {
        $board->delete();
        $this->generateLog($request->user(), "deleted this board ({$board->id}).");

        return response([
            'record' => 'Board deleted'
        ]);
    }
}
