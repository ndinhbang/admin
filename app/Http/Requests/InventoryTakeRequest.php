<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryTakeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->user()->hasAnyRole(['admin', 'superadmin']) || $this->user()->can('manage.inventory')) {
            return true;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->routeIs(['inventory_take.store', 'inventory_take.update'])) {
            return [
                'supplies'            => ['bail', 'array', 'max:25'],
                'code'                => ['bail', 'nullable', 'sometimes', 'alpha_dash', 'max:20'],
                'on_date'             => ['bail', 'required', 'date'],
                'supplies.*.qty_actual' => [
                    'bail',
                    'numeric',
                    Rule::requiredIf(count($this->input('supplies')) > 0),
                ]
            ];
        }

        return [];
    }

    public function messages()
    {
        return [
            'supplies.*.id.required'       => 'Bạn chưa chon nguyện liệu ',
            'supplies.*.id.integer'        => 'ID nguyên liệu phải là số ',
            'supplies.*.qty_actual.numeric'  => 'Số lượng thực tế phải là số',
        ];
    }
}
