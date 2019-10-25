<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
    public function rules()
    {
        if ($this->routeIs('profile.change-password')) {
            return [
                'current_password' => 'bail|required',
                'new_password' => 'bail|required|different:current_password|min:6',
                'new_password_confirmation' => 'bail|required|same:new_password',
            ];
        }
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
