<?php

namespace App\Http\Requests;

use App\Models\Role;
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
        // Todo: need test carefully
        $user  = $this->user();
        if (!is_null($currentPlace = currentPlace())
            && $user->can('settings.employees')) {
            return true;
        }
        return false;
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
                'phone' => 'bail|unique:users|min:10|max:11',
                'email' => 'bail|unique:users',
                'password' => 'bail|required',
                'role_names' => 'bail|required|array|min:1|max:5'
            ];
        }

        if ($this->routeIs('employee.update')) {
            return [
                'display_name' => 'bail|required',
                'name' => 'bail|required|unique:users,name,'.$this->uuid.',uuid',
                'phone' => 'bail|min:10|max:11|unique:users,phone,'.$this->uuid.',uuid',
                'email' => 'bail|unique:users,email,'.$this->uuid.',uuid',
                'role_names' => 'bail|required|array|min:1|max:5'
            ];
        }

        if ($this->routeIs('employee.update-avatar')) {
            return [
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];
        }
        return [];
    }

    public function messages()
    {
        return [
            'name.unique'       => 'Tên đăng nhập đã có người sử dụng',
            'phone.unique'       => 'Số điện thoại đã có người sử dụng',
            'email.unique'       => 'Email đã có người sử dụng',
            'avatar.mimes'       => 'Định dạng ảnh không hợp lệ',
        ];
    }
}
