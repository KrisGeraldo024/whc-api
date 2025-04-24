<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\{
    Request,
    Response
};
use App\Http\Requests\TestimonialStoreRequest;
use App\Services\Feedback\TestimonialService;

class TestimonialController extends Controller
{
    protected $testimonialService;

    public function __construct (TestimonialService $testimonialService)
    {
        $this->testimonialService = $testimonialService;
    }

    public function index (Request $request): Response
    {
        return $this->testimonialService->index($request);
    }

    public function store (TestimonialStoreRequest $request): Response
    {
        return $this->testimonialService->store($request);
    }
  
    public function show (Testimonial $testimonial, Request $request): Response
    {
        return $this->testimonialService->show($testimonial, $request);
    }
    
    public function update (Testimonial $testimonial, TestimonialStoreRequest $request): Response
    {
        return $this->testimonialService->update($testimonial, $request);
    }
    
    public function destroy (Testimonial $testimonial, Request $request): Response
    {
        return $this->testimonialService->destroy($testimonial, $request);
    }
}