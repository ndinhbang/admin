<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        if ($this->routeIs('admin.user.store') || $this->routeIs('admin.user.store')) {
            return [
                'display_name' => 'bail|required',
                'name' => 'bail|required|unique:users',
                'phone' => 'bail|required|unique:users|min:10|max:11',
                'email' => 'bail|required|unique:users',
                'password' => 'bail|required',
            ];
        }

        if ($this->routeIs('admin.user.update') || $this->routeIs('admin.user.update')) {
            return [
                'display_name' => 'bail|required',
                'name' => 'bail|required|unique:users,name,'.$this->uuid.',uuid',
                'phone' => 'bail|min:10|max:11|unique:users,phone,'.$this->uuid.',uuid',
                'email' => 'bail|unique:users,email,'.$this->uuid.',uuid'
            ];
        }
        return [];
    }
}
