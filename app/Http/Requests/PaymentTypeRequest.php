<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentTypeRequest extends FormRequest
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
            'title'       => 'required',
            'summary'     => 'required',
            'content'     => 'sometimes',
            'order'       => 'required|integer',
            'enabled'     => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'title.required'      => 'Title is required',
            'summary.required'    => 'Summary is required',
            'order.required'      => 'Order is required',
        ];
    }
}
