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
        if ($this->routeIs('login')) {
            return [
                'phone' => 'bail|required|digits_between:10,11',
                'password' => 'bail|required|string|min:6|max:191',
            ];
        }

        return [];
    }

    public function messages()
    {
        return [
            'phone.required' => 'Số điện thoại bắt buộc phải nhập.',
            'phone.digits_between' => 'Số điện thoại không hợp lệ.',
        ];
    }
}
