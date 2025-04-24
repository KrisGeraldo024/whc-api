<?php

namespace App\Services\Subscribe;

use Illuminate\Support\Facades\{
    Validator
};
use Illuminate\Http\Response;
use App\Models\{
    Subscribe
};
use App\Mail\{
    AdminSubscribeMail,
    SubscribeMail
};
use App\Traits\GlobalTrait;
use App\Jobs\SendSubscribeMail;

class SubscribeService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * SubscribeService index
     * @param  Request  $request
     * @return Response
     */
    public function index ($request): Response
    {
        $inquiries = Subscribe::orderByDesc('created_at')
        ->when($request->filled('all'), function ($query) {
            return $query->get();
        }, function ($query) {
            return $query->paginate(20);
        });

        return response([
            'records' => $inquiries
        ]);
    }

    /**
     * SubscribeService subscribe
     * @param  Request  $request
     * @return Response
     */
    public function subscribe ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'email_address' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $subscribe = Subscribe::where('email_address', $request->email_address)->exists();

        if ($subscribe) {
            return response([
                'errors' => ['This email address is already subscribed.']
            ], 400);
        } else {
            $subscribe = Subscribe::create([
                'email_address' => $request->email_address,
                'subscribe' => 1
            ]);
            SendSubscribeMail::dispatch($subscribe);
        }

        return response([
            'record' => $subscribe
        ]);
    }

    /**
     * SubscribeService unsubscribe
     * @param  Request  $request
     * @return Response
     */
    public function unsubscribe ($request): Response
    {
        if (!$request->filled('t')) {
            return response([
                'errors' => ['Oop! Something went wrong. Please try again.']
            ], 400);
        }

        $subscribe = Subscribe::find($request->t);

        if (!$subscribe->subscribe) {
            return response([
                'errors' => ['This email address is already unsubscribed.']
            ], 400);
        } else {
            $subscribe->update([
                'subscribe' => 0
            ]);
            SendSubscribeMail::dispatch($subscribe);
        }

        return response([
            'record' => $subscribe
        ]);
    }
}
