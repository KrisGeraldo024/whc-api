<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExecutiveRequest extends FormRequest
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
            'position'      => 'required',
            // 'biography'     => 'required',
            'order'         => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'position.required' => 'Position is required',
            // 'biography.required' => 'Biography is required',
            'order.required' => 'Order is required',
        ];
    }
}
