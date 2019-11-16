<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AreaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->user()->hasAnyRole(['admin', 'superadmin']) || $this->user()->can('pos')) {
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
        if ($this->routeIs(['areas.store', 'areas.update'])) {
            return [
                'name'            => ['bail', 'required', 'string', 'max:50'],
                'alsoCreateTable' => ['bail', 'boolean'],
                'table_quantity'  => ['bail', 'integer',
                    Rule::requiredIf($this->input('alsoCreateTable', false))
                ],

            ];
        }

        return [
            'page' => ['bail', 'integer'],
            'per_page' => ['bail', 'integer']
        ];
    }
}
