<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int per_page
 */
class SegmentRequest extends FormRequest
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
                ->can('crm.customers') ) {
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
        return [
            'customers'                                    => [
                'bail',
                'array',
                Rule::requiredIf(empty($this->conditions)),
            ],
            'customers.*.uuid'                             => [
                'bail',
                'alpha_dash',
                'size:21',
                'required',
            ],
            'conditions'                                   => [
                'bail',
                'array',
                'max:6',
                Rule::requiredIf(empty($this->customers)),
            ],
            'conditions.*.property'                        => [
                'bail',
                'required',
                'array',
                'max:7',
            ],
            'conditions.*.property.operator'               => [
                'bail',
                'required',
                'array',
                'max:2',
            ],
            'conditions.*.property.propertyValue'          => [
                'bail',
                'required',
                'string',
                Rule::in([
                    'totalOrders',
                    'totalOrderValue',
                    'balance',
                    'birthMonth',
                    'daysSinceLastPurchase',
                    'gender',
                ]),
            ],
            'conditions.*.property.operator.operatorValue' => [
                'bail',
                'required',
                'string',
                Rule::in([
                    'eq',
                    'ne',
                    'gt',
                    'gte',
                    'lt',
                    'lte',
                ]),
            ],
            'conditions.*.property.value'                  => [
                'bail',
                'required',
                'alpha_num',
            ],
            'conditions.*.property.propertyLabel'          => [
                'bail',
                'string',
                'max:191',
            ],
            'conditions.*.property.placeholer'             => [
                'bail',
                'string',
                'max:191',
            ],
        ];
    }
}
