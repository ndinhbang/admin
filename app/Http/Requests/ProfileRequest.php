<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ProfileRequest extends FormRequest
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
    public function rules(Request $request)
    {
        if ($this->routeIs('profile.change-password')) {
            return [
                'current_password' => 'bail|required',
                'new_password' => 'bail|required|different:current_password|min:6',
                'new_password_confirmation' => 'bail|required|same:new_password',
            ];
        }

        if ($this->routeIs('profile.update-avatar')) {
            return [
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];
        }

        if ($this->routeIs('profile.update-profile')) {
            return [
                'display_name' => 'bail|required',
                'name' => 'bail|required|unique:users,name,'.$request->user()->id,
                'phone' => 'bail|min:10|max:11|unique:users,phone,'.$request->user()->id,
                'email' => 'bail|unique:users,email,'.$request->user()->id,
            ];
        }

        return [];
    }

    public function messages()
    {
        return [
            'current_password.required' => 'Số điện thoại bắt buộc phải nhập.',
            'new_password.different' => 'Mật khẩu mới cần phải khác với mật khẩu cũ',
            'new_password_confirmation.same' => 'Xác nhận cần phải giống với Mật khẩu mới',
        ];
    }
}
