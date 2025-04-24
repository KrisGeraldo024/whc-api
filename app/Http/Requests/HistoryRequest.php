<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\WordCount;


class HistoryRequest extends FormRequest
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
            'title'          => 'required|max:255',
            'subtitle'       => ['required', new WordCount(152)],
            'year'           => 'required',
            'order'          => 'required|integer',

        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Title is required',
            'title.max' => 'Title must be limited to 150 words',
            'subtitle.required' => 'Subtitle is required',
            'subtitle.max' => 'Subtitle must be limited to 150 words',
            'year.required' => 'Year is required',
            'order.required' => 'Order is required',
        ];
    }
}
