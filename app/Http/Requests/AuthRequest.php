<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
        if ($this->routeIs('auth.login')) {
            return [
                'phone' => 'bail|required|digits_between:10,11',
                'password' => 'bail|required|min:6|max:191',
            ];
        }

        if ($this->routeIs('auth.password')) {
            return [
                'phone' => 'bail|required|digits_between:10,11',
            ];
        }
        
        if ($this->routeIs('auth.validate-password')) {
            return [
                'token' => 'bail|required',
            ];
        }
        
        if ($this->routeIs('auth.reset')) {
            return [
                'email' => 'bail|required|email|exists:users,email',
                'password' => 'bail|required|min:6|max:191',
            ];
        }

        return [];
    }

    public function messages()
    {
        return [
            'phone.required' => 'Số điện thoại bắt buộc phải nhập.',
            'phone.digits_between' => 'Số điện thoại không hợp lệ.',
            'email.exists' => 'Tài khoản không tồn tại.',
        ];
    }
}
