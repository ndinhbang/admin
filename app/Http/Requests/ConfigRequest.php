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
                    Rule::dimensions()->maxWidth(1920)->maxHeight(1080),
                ],
            ];
        }
        return [
            //
        ];
    }
}
