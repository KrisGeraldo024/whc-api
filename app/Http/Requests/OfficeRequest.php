<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfficeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'office_name'   => 'required',
            'address'       => 'required',
            'link_address'  => 'required',
            'order'         => 'required|integer',
            'enabled'       => 'required|integer'
        ];
    }
}
