<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentOptionRequest extends FormRequest
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
            'steps.*'               => 'sometimes|mimes:jpeg,png,jpg,webp|max:1000',
            'enabled'               => 'required|integer',
            'is_other_services'     => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'steps.*.mimes'        => 'Each logo must be a JPEG, PNG, JPG, or WebP file',
            'steps.*.max'          => 'Each logo may not be greater than 1MB in size',
        ];
    }
}
