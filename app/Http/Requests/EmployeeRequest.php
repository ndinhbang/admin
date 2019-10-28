<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
        if ($this->routeIs('employee.store')) {
            return [
                'display_name' => 'bail|required',
                'name' => 'bail|required|unique:users',
                'phone' => 'bail|unique:users',
                'email' => 'bail|unique:users',
                'password' => 'bail|required',
            ];
        }

        if ($this->routeIs('employee.update-avatar')) {
            return [
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:1024',
            ];
        }
    }
}
