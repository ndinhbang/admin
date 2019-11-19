<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PosProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->user()->hasAnyRole(['admin', 'superadmin'])
            || $this->user()->can('pos')) {
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
        return [
            'is_hot' => ['bail', 'sometimes', 'boolean'],
            'opened' => ['bail', 'sometimes', 'boolean'],
            'state'  => ['bail', 'sometimes', 'boolean'],
            'keyword'  => ['bail', 'sometimes', 'string', 'max:191'],
        ];
    }
}
