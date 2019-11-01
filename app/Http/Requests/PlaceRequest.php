<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        if ($this->routeIs('place.my')) {
            return [
                'title'   => 'required',
                'code'   => 'required|unique:places',
                'address' => 'required'
            ];
        }

        if ($this->routeIs('place.update')) {
            return [
                'title'   => 'required',
                'code'   => 'required|unique:places,code,'.$this->id,
                'address' => 'required'
            ];
        }

        if ($this->routeIs('place.update-logo')) {
            return [
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:1024',
            ];
        }

        return [];
    }
}