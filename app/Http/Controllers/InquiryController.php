<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\Inquiry;
use App\Services\Inquiry\InquiryService;

class InquiryController extends Controller
{
    /**
     * @var InquiryService
     */
    protected $inquiryService;

    /**
     * InquiryController constructor
     * @param InquiryService $InquiryService
     */
    public function __construct(InquiryService $inquiryService)
    {
        $this->inquiryService = $inquiryService;
    }

    /**
     * InquiryController index
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->inquiryService->index($request);
        // return response([
        //     'res' => $request->type
        // ]);
    }

    /**
     * InquiryController update
     * @param  Inquiry  $inquiry
     * @param  Request  $request
     * @return Response
     */
    public function update(Inquiry $inquiry, Request $request): Response
    {
        return $this->inquiryService->update($inquiry, $request);
    }

    /**
     * InquiryController inquiry
     * @param  Request  $request
     * @return Response
     */
    public function sendInquiry(Request $request): Response
    {
        return $this->inquiryService->inquiry($request);
    }

    public function salesInquiry(Request $request): Response
    {
        return $this->inquiryService->salesInquiry($request);
    }

    public function sendApplication(Request $request): Response
    {
        return $this->inquiryService->application($request);
    }

    public function brokerForm(Request $request): Response
    {
        return $this->inquiryService->brokerForm($request);
    }

    public function exportInquiries(Request $request)
    {
        return $this->inquiryService->exportInquiries($request);
    }
}
