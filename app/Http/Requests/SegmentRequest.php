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
        if ( $this->user()->hasAnyRole([ 'admin', 'superadmin' ])
            || $this->user()->can('crm.customers') ) {
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
        if ( $this->routeIs([ 'segment.create', 'segment.update' ]) ) {
            return [
                'fixedCustomers'                   => [
                    'bail',
                    'array',
                    Rule::requiredIf(empty($this->conditions)),
                ],
                'conditions'                       => [
                    'bail',
                    'array',
                    'max:6',
                    Rule::requiredIf(empty($this->customers)),
                ],
                'fixedCustomers.*.uuid'            => [ 'bail', 'required', 'alpha_dash', 'size:21', ],
                'conditions.*.property'            => [ 'bail', 'required', 'array', 'max:7' ],
                'conditions.*.property.value'      => [ 'bail', 'required', 'alpha_num' ],
                'conditions.*.property.name'       => [
                    'bail',
                    'required',
                    'string',
                    Rule::in(
                        [
                            'totalOrders',
                            'totalOrderValue',
                            'balance',
                            'birthMonth',
                            'daysSinceLastPurchase',
                            'gender',
                        ]
                    ),
                ],
                'conditions.*.property.operator'   => [
                    'bail',
                    'required',
                    'string',
                    Rule::in([ 'eq', 'ne', 'gt', 'gte', 'lt', 'lte' ]),
                ],
            ];
        }
        return [];
    }
}
