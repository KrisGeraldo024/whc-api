<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillerRequest extends FormRequest
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
          'company_name'          => 'required',
          'bpi_biller_name'       => 'required',
          'gcash_biller_name'     => 'required',
        ];
    }

    public function messages()
    {
        return [
          'company_name.required'       => 'Name is required',
          'bpi_biller_name.required'    => 'Position is required',
          'gcash_biller_name.required'  => 'Biography is required',
        ];
    }
}
