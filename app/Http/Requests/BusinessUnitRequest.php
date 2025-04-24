<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BusinessUnitRequest extends FormRequest
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
            'name'          => 'required',
            'order'         => 'required',
            'featured'      => 'required',
            'description_1' => 'required',
            'description_2' => 'required',
            'tagline'       => 'required'
        ];
    }

    public function messages()
    {
        return [
            'title.required'          => 'Title is required',
            'order.required'          => 'Order is required',
            'featured.required'       => 'Featured is required',
            'description_1.required'  => 'Description 1 is required',
            'description_2.required'  => 'Description 2 is required',
            'tagline.required'        => 'Tagline is required'
        ];
    }
}
