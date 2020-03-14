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
        if ( $this->routeIs([ 'segment.store', 'segment.update' ]) ) {
            return [
                'conditions'                     => [
                    'bail',
                    'array',
                    'max:6',
                    Rule::requiredIf(empty($this->fixedCustomers)),
                ],
                'conditions.*.property'          => [ 'bail', 'required', 'array', 'size:3' ],
                'conditions.*.property.value'    => [ 'bail', 'required', 'alpha_num' ],
                'conditions.*.property.name'     => [
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
                'conditions.*.property.operator' => [
                    'bail',
                    'required',
                    'string',
                    Rule::in([ 'eq', 'ne', 'gt', 'gte', 'lt', 'lte' ]),
                ],
                'fixedCustomers'                 => [
                    'bail',
                    'array',
                    Rule::requiredIf(empty($this->conditions)),
                ],
                'fixedCustomers.*.uuid'          => [ 'bail', 'required', 'alpha_dash', 'size:21', ],
            ];
        }
        return [];
    }

    public function attributes()
    {
        return [
            'conditions.*.property.name' => 'Thuộc tính',
        ];
    }

    public function messages()
    {
        return [
            'fixedCustomers.required'     => 'Bạn chưa nhập khách hàng cho nhóm khách hàng',
            'conditions.required'         => 'Bạn chưa nhập khách hàng cho nhóm khách hàng',
            'conditions.*.property.value' => 'Bạn chưa nhập giá trị cho thuộc tính',
            'conditions.*.property.size'  => 'Bạn chưa nhập giá trị cho thuộc tính',
        ];
    }
}
