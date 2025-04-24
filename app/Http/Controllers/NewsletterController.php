<?php

namespace App\Http\Controllers;

use App\Services\Newsletter\MailchimpService;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    private MailchimpService $mailchimpService;

    public function __construct(MailchimpService $mailchimpService)
    {
        $this->mailchimpService = $mailchimpService;
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $result = $this->mailchimpService->subscribe($request->email);

        if ($result['success']) {
            return response()->json($result, 200);
        }

        return response()->json($result, 422);
    }
}
