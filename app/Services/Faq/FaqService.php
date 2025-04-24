<?php

namespace App\Services\Faq;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Faq;
use App\Traits\GlobalTrait;

class FaqService
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
        $records = Faq::orderBy('order')
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
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'answer'   => 'required',
            'type'     => 'required|in:general,product',
            'order'    => 'required|integer',
            'enabled'  => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = Faq::create([
            'question' => $request->question,
            'answer'   => $request->answer,
            'type'     => $request->type,
            'order'    => $request->order,
            'enabled'  => $request->enabled
        ]);

        $this->generateLog($request->user(), "added this faq ({$record->id}).");

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
    public function show ($faq, $request): Response
    {
        $this->generateLog($request->user(), "viewed this faq ({$faq->id}).");

        return response([
            'record' => $faq
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($faq, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'answer'   => 'required',
            'type'     => 'required|in:general,product',
            'order'    => 'required|integer',
            'enabled'  => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $faq->update([
            'question' => $request->question,
            'answer'   => $request->answer,
            'type'     => $request->type,
            'order'    => $request->order,
            'enabled'  => $request->enabled
        ]);

        $this->generateLog($request->user(), "updated this faq ({$faq->id}).");

        return response([
            'record' => $faq
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($faq, $request): Response
    {
        $faq->delete();
        $this->generateLog($request->user(), "deleted this faq ({$faq->id}).");

        return response([
            'record' => 'Faq deleted'
        ]);
    }
}
