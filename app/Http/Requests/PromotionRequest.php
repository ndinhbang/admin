<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromotionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ( $this->user()->hasAnyRole([ 'admin', 'superadmin' ])
            || $this->user()->can('manage.promotions') ) {
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
            'name'                          => [ 'bail', 'required', 'string', 'max:50' ],
            'code'                          => [ 'bail', 'string', 'max:50' ],
            'type'                          => [ 'bail', 'required', 'string', Rule::in([ 'product', 'order' ]) ],
            'state'                         => [ 'bail', 'required', 'numeric', Rule::in([ 0, 1, 2 ]) ],
            'applied'                       => [ 'bail', 'required', 'array', 'size:6' ],
            'applied.allCustomer'           => [ 'bail', 'required', 'boolean' ],
            'applied.someSegment'           => [ 'bail', 'required', 'boolean' ],
            'applied.someCustomer'          => [ 'bail', 'required', 'boolean' ],
            'applied.allProduct'            => [ 'bail', 'required', 'boolean' ],
            'applied.someCategory'          => [ 'bail', 'required', 'boolean' ],
            'applied.someProduct'           => [ 'bail', 'required', 'boolean' ],
            'required_code'                 => [ 'bail', 'required', 'boolean' ],
            'is_limited'                    => [ 'bail', 'required', 'boolean' ],
            'limit_qty'                     => [ 'bail', 'required', 'numeric', 'min:0' ],
            'remain_qty'                    => [ 'bail', 'required', 'numeric', 'min:0', 'lte:limit_qty' ],
            'from'                          => [ 'bail', 'required', 'boolean' ],
            'to'                            => [ 'bail', 'required', 'boolean' ],
            'total'                         => [ 'bail', 'required', 'boolean' ],
            'rule'                          => [ 'bail', 'required', 'array', 'max:6', 'min:1' ],
            'rule.all'                      => [ 'bail', 'required', 'array', 'size:3' ],
            'rule.all.minimumQty'           => [ 'bail', 'required', 'numeric', 'min:0' ],
            'rule.all.discountValue'        => [ 'bail', 'required', 'numeric', 'min:0' ],
            'rule.all.discountType'         => [ 'bail', 'required', 'string', 'in:%,đ' ],
            'rule.category'                 => [ 'bail', 'required', 'array' ],
            'rule.category.*.uuid'          => [ 'bail', 'required', 'string', 'size:21' ],
            'rule.category.*.name'          => [ 'bail', 'required', 'string', 'max:191' ],
            'rule.category.*.minimumQty'    => [ 'bail', 'required', 'numeric', 'min:0' ],
            'rule.category.*.discountValue' => [ 'bail', 'required', 'numeric', 'min:0' ],
            'rule.category.*.discountType'  => [ 'bail', 'required', 'string', 'in:%,đ' ],
            'rule.product'                  => [ 'bail', 'required', 'array' ],
            'rule.product.*.uuid'           => [ 'bail', 'required', 'string', 'size:21' ],
            'rule.product.*.name'           => [ 'bail', 'required', 'string', 'max:191' ],
            'rule.product.*.minimumQty'     => [ 'bail', 'required', 'numeric', 'min:0' ],
            'rule.product.*.discountValue'  => [ 'bail', 'required', 'numeric', 'min:0' ],
            'rule.product.*.discountType'   => [ 'bail', 'required', 'string', 'in:%,đ' ],
            'rule.order'                    => [ 'bail', 'required', 'array' ],
            'rule.order.*.minimumPrice'     => [ 'bail', 'required', 'numeric', 'min:0' ],
            'rule.order.*.discountValue'    => [ 'bail', 'required', 'numeric', 'min:0' ],
            'rule.order.*.discountType'     => [ 'bail', 'required', 'string', 'in:%,đ' ],
            'customers'                     => [ 'bail', 'required', 'array' ],
            'segments'                      => [ 'bail', 'required', 'array' ],
            'note'                          => [ 'bail', 'string', 'max:191' ],
        ];
    }
}
