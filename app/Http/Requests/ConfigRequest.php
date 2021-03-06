<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfigRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ( $this->user()
                ->hasAnyRole([
                    'admin',
                    'superadmin',
                ])
            || $this->user()
                ->can('settings.print-form') ) {
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
        if ( $this->routeIs([ 'config.screen2nd' ]) ) {
            return [
                'useImage' => 'bail|required|boolean',
                'image'     => 'bail|sometimes|nullable|string',
                'imageFile' => [
                    'bail',
                    'nullable',
                    'image',
                    'mimes:jpeg,jpg,png,gif',
                    'max:2048',
                    Rule::requiredIf(
                        !$this->input('useImage', false)
                        && !$this->input('image', false)
                    ),
                    Rule::dimensions()->maxWidth(5000)->maxHeight(3000),
                ],
            ];
        }
        if ($this->routeIs(['config.print'])) {
            return [
                'config'   => 'bail|required|array',
                'config.*.title' => 'bail|sometimes|nullable|string|max:25',
                'config.*.printer' => 'bail|sometimes|nullable|string|max:191',
                'config.*.menu' => 'bail|array',
                'config.*.print_draft' => 'bail|boolean',
                'config.*.print_when_accepted' => 'bail|boolean',
                'config.*.print_when_paid' => 'bail|boolean',
                'config.*.print_when_notified' => 'bail|boolean',
            ];
        }
        if ($this->routeIs(['config.sale'])) {
            return [
                'config'   => 'bail|required|array'
            ];
        }
        return [
            //
        ];
    }
    
    public function messages()
    {
        return [
            'name.unique'       => 'Tên đăng nhập đã có người sử dụng',
            'phone.unique'       => 'Số điện thoại đã có người sử dụng',
            'email.unique'       => 'Email đã có người sử dụng',
        ];
    }
}
