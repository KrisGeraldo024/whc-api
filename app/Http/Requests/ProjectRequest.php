<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
            'name'              => 'required|unique:projects',
            //'slug'              => 'required',
            
            'location_id'       => 'required',
            'project_status_id' => 'required',
            'property_id'        => 'required', 

            //'architect'         => 'required',
            //'partners'          => 'required',
            'land_area'         => 'required|integer',
            //'phases'            => 'required|integer',
            'full_address'      => 'required',

            'enabled'           => 'required',
            'featured'          => 'required',
            'order'             => 'required|integer',

            //'title'              => 'required',
            'content_col_1'      => 'required',
            //'content_col_2'      => 'required',

            //'virtual_tour_link'      => 'required',

            'main_image.*'      => 'required|mimes:jpeg,png,jpg,webp|max:3000',
            'banner_image.*'    => 'required|mimes:jpeg,png,jpg,webp|max:1000',
            //'location_map.*'    => 'required|mimes:jpeg,png,jpg,webp|max:1000',
           
        ];
    }

    public function messages()
    {
        return [
            'name.required'                  => 'required',
            'name.unique'                    => 'Project name has already been taken',
            
            'location_id.required'           => 'required',
            'project_status_id.required'     => 'required',
            'property_id.required'           => 'required', 

            //'architect.required'             => 'required',
            //'partners.required'              => 'required',
            'land_area.required'             => 'required|integer',
            //'phases.required'                => 'required|integer',
            'full_address.required'          => 'required',

            'enabled.required'               => 'required',
            'featured.required'              => 'required',
            'order.required'                 => 'required|integer',

            //'title.required'                 => 'required',
            'content_col_1.required'         => 'required',
            //'content_col_2.required'         => 'required',

            //'virtual_tour_link'      => 'required',

        ];
    }
}
