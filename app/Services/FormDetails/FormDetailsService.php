<?php

namespace App\Services\FormDetails;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\FormDetails;
use App\Traits\GlobalTrait;

class FormDetailsService
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
    // public function index ($request): Response
    // {
    
    // }

    public function store ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'parent_id'        => 'required',
            'fullname'         => 'required|integer',
            'firstname'        => 'required|integer',
            'middlename'       => 'required|integer',
            'lastname'         => 'required|integer',
            'age'              => 'required|integer',
            'email'            => 'required|integer',
            'phone'            => 'required|integer',
            'address'          => 'required|integer',
            'province'         => 'required|integer',
            'city'             => 'required|integer',
            'barangay'         => 'required|integer',
            'postal'           => 'required|integer',
            'houseno'          => 'required|integer',
            'street'           => 'required|integer',
            'hearing_aid_user' => 'sometimes|integer',
            'message'          => 'required|integer',
            'submit_label'     => 'sometimes',
            'enabled'          => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $form = FormDetails::where('parent_id', $request->parent_id)->first();
        if( is_null($form)){
            $form = FormDetails::create([
                'parent_id'     => $request->parent_id,
                'fullname'      => $request->fullname,
                'firstname'     => $request->firstname,
                'middlename'    => $request->middlename,
                'lastname'      => $request->lastname,
                'age'           => $request->age,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'address'       => $request->address,
                'province'      => $request->province,
                'city'          => $request->city,
                'barangay'      => $request->barangay,
                'postal'        => $request->postal,
                'street'        => $request->street,
                'houseno'       => $request->houseno,
                'message'       => $request->message,
                'hearing_aid_user' => $request->hearing_aid_user,
                'submit_label'  => $request->submit_label,
                'enabled'       => $request->enabled
            ]);
        }
        else {
            $form->update([
                'parent_id'     => $request->parent_id,
                'fullname'      => $request->fullname,
                'firstname'     => $request->firstname,
                'middlename'    => $request->middlename,
                'lastname'      => $request->lastname,
                'age'           => $request->age,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'address'       => $request->address,
                'province'      => $request->province,
                'city'          => $request->city,
                'barangay'      => $request->barangay,
                'postal'        => $request->postal,
                'street'        => $request->street,
                'houseno'       => $request->houseno,
                'hearing_aid_user' => $request->hearing_aid_user,
                'message'       => $request->message,
                'submit_label'  => $request->submit_label,
                'enabled'       => $request->enabled
            ]);
        }

        $this->generateLog($request->user(), "added this form ({$form->id}).");

        return response([
            'record' => $form
        ]);
    }

    public function show ($parent_id): Response
    {
        $form = FormDetails::where('parent_id', $parent_id)->first();
        return response([
            'record' => $form
        ]);
    }
}
