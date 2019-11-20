<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PosOrderRequest extends FormRequest
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
        if ($this->routeIs('pos.orders.update')) {
            $countItems = 0;
            if (!empty($items = $this->input('items', []))) {
                $countItems = count($items);
            }
            return [
                'items' => ['sometimes', 'array', 'max:100'],
                'items.*.uuid' => [
                    Rule::requiredIf($countItems > 0),
                    'string',
                    'size:21',
                ],
                'items.*.quantity' => [
                    Rule::requiredIf($countItems > 0),
                    'numeric',
                    'min:1',
                ],
                'items.*.note' => [
                    'sometimes',
                    'string',
                    'max:191',
                ],
                'table' => [
                    'sometimes', 'array'
                ],
                'table.uuid' => [
                    Rule::requiredIf(!empty($this->input('table', []))),
                    'size:21'
                ]
            ];
        }
        if ($this->routeIs(['pos.orders.update-item'])) {
            return [
                'quantity' => 'required|numeric|min:1|max:999',
            ];
        }
        return [
            'kind' => 'sometimes|numeric',
        ];
    }
}
