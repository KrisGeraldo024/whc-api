<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\{
    Request,
    Response
};
use App\Services\Email\EmailService;


class EmailController extends Controller
{
    protected $emailService;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct (EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index (Request $request): Response
    {
        return $this->emailService->index($request);
    }


    public function store (Request $request): Response
    {
        return $this->emailService->store($request);
    }


    public function show (Email $email, Request $request): Response
    {
        return $this->emailService->show($email, $request);
    }

    public function update (Email $email, Request $request): Response
    {
        return $this->emailService->update($email, $request);
    }


    public function destroy (Email $email, Request $request): Response
    {
        return $this->emailService->destroy($email, $request);
    }
}
