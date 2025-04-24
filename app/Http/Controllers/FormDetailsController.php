<?php

namespace App\Http\Controllers;

use App\Models\FormDetails;
use Illuminate\Http\{
    Request,
    Response
};
use App\Services\FormDetails\FormDetailsService;


class FormDetailsController extends Controller
{
    /**
     * @var FormDetailsService
     */
    protected $formDetailsService;

    /**
     * FaqController constructor
     * @param FormDetailsService $formDetailsService
     */
    public function __construct (FormDetailsService $formDetailsService)
    {
        $this->formDetailsService = $formDetailsService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): Response
    {
        return $this->formDetailsService->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FormDetails  $formDetails
     * @return \Illuminate\Http\Response
     */
    public function show(FormDetails $formDetails, $parent_id): Response
    {
        //
        return $this->formDetailsService->show($parent_id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FormDetails  $formDetails
     * @return \Illuminate\Http\Response
     */
    public function edit(FormDetails $formDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FormDetails  $formDetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FormDetails $formDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FormDetails  $formDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy(FormDetails $formDetails)
    {
        //
    }
}
