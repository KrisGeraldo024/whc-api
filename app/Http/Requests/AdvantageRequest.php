<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdvantageRequest extends FormRequest
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
            'title'      => 'required',
            'content'   => 'required',
            'order'     => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Title is required',
            'content.required' => 'Content is required',
            'order.required' => 'Order is required',
        ];
    }
}
