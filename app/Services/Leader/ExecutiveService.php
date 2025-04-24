<?php

namespace App\Services\Leader;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    DB,
    Validator
};
use App\Models\Executive;
use App\Models\ExecutivePosition;
use App\Traits\GlobalTrait;

class ExecutiveService
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
        $records = Executive::orderBy('order')
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
        try {
            DB::beginTransaction();

            $record = Executive::create([
                'name'      => $request->name,
                // 'biography' => $request->biography,
                'order'     => $request->order,
                'enabled'    => $request->enabled,

            ]);

            if ($request->has('position')) {
                foreach ($request->position as $index => $position) {
                    ExecutivePosition::create([
                        'title' => $position['name'],
                        'executive_id' => $record->id,
                        'order' => $position['order']
                    ]);
                }
            }


            // $this->addImages('executive', $request, $record, 'icon');
            $this->addImages('executive', $request, $record, 'main_image');
            // $this->metatags($record, $request);
            
            $this->generateLog($request->user(), "added this executive ({$record->id}).");

            DB::commit();

            return response([
                'record' => $record
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response([
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * FaqService show
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function show ($executive, $request): Response
    {
        $executive->load('images', 'metadata', 'position');

        $this->generateLog($request->user(), "viewed this executive ({$executive->id}).");

        return response([
            'record' => $executive
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($executive, $request): Response
    {
        try {
            DB::beginTransaction();
            $executive->update([
                'name'      => $request->name,
                'order'     => $request->order,
                'enabled'    => $request->enabled,
            ]);

            $positions = $executive->position;
            if ($request->has('position')) {
                foreach ($positions as $pos) {
                    $exec_position = ExecutivePosition::find($pos->id);
                    $exec_position->delete();
                }

                foreach ($request->position as $index => $position) {
                    
                    ExecutivePosition::create([
                        'title' => $position['name'],
                        'executive_id' => $executive->id,
                        'order' => $position['order']
                    ]);
                }
            }

            // $this->updateImages('executive', $request, $executive, 'icon');
            $this->updateImages('executive', $request, $executive, 'main_image');
            $this->metatags($executive, $request);

            $this->generateLog($request->user(), "updated this executive ({$executive->id}).");
            DB::commit();

            return response([
                'record' => $executive
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response([
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($executive, $request): Response
    {
        $executive->delete();
        $this->generateLog($request->user(), "deleted this executive ({$executive->id}).");

        return response([
            'record' => 'Executive deleted'
        ]);
    }
}
