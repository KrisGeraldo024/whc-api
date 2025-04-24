<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentPlatformRequest extends FormRequest
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
            'title' => [
                'required',
                Rule::unique('payment_platforms')
                    ->whereNull('deleted_at')
                    ->where('payment_method_id', $this->payment_method_id)
                    ->ignore(optional($this->payment_platform)->id), // Safely handle null
            ],
            // 'logo.*'      => 'required|mimes:jpeg,png,jpg,webp|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'title.required'      => 'Title is required',
            // 'logo.*.required'     => 'Each logo field is required',
            // 'logo.*.mimes'        => 'Each logo must be a JPEG, PNG, JPG, or WebP file',
            // 'logo.*.max'          => 'Each logo may not be greater than 1MB in size',
        ];
    }
}
