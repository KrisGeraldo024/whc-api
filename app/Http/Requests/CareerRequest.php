<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CareerRequest extends FormRequest
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
          'title'           => 'required',
          'description'     => 'sometimes',
          'qualification'   => 'required',
        //   'locations'       => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required'          => 'Title is required',
            'qualification.required'  => 'Qualification is required',
            // 'locations.required'      => 'Location is required',
        ];
    }
}
