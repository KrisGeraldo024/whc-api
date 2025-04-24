<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectAwardRequest extends FormRequest
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
            'name'      => 'required',
            'content'   => 'required',
            'year'      => 'required',
            'order'     => 'required',
            'enabled'   => 'required',
            'featured'   => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'content.required' => 'Content is required',
            'year.required' => 'Year is required',
            'order.required' => 'Order is required',
            'enabled.required' => 'Enabled is required',
            'featured.required' => 'Featured is required',
        ];
    }
}
