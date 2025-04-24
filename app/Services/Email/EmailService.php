<?php

namespace App\Services\Email;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Email;
use App\Traits\GlobalTrait;

class EmailService
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
        $records = Email::orderBy('order')
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
            'department'    => 'required',
            'email'         => 'required',
            'order'         => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $record = Email::create([
            'department'    => $request->department,
            'email'         => $request->email,
            'order'         => $request->order
        ]);

        $this->generateLog($request->user(), "added this email ({$record->id}).");

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
    public function show ($email, $request): Response
    {
        $this->generateLog($request->user(), "viewed this faq ({$email->id}).");

        return response([
            'record' => $email
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($email, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'department'    => 'required',
            'email'         => 'required',
            'order'         => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $email->update([
            'department'    => $request->department,
            'email'         => $request->email,
            'order'         => $request->order
        ]);

        $this->generateLog($request->user(), "updated this faq ({$email->id}).");

        return response([
            'record' => $email
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($email, $request): Response
    {
        $email->delete();
        $this->generateLog($request->user(), "deleted this faq ({$email->id}).");

        return response([
            'record' => 'Email deleted'
        ]);
    }
}
