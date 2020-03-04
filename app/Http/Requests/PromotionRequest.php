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
        if ( $this->routeIs([ 'promotion.store', 'promotion.update' ]) ) {
            $applied   = $this->input('applied');
            $isLimited = $this->input('is_limited', false);
            return [
                'name'                          => [ 'bail', 'required', 'string', 'max:50' ],
                'code'                          => [ 'bail', 'nullable', 'string', 'max:50', 'alpha_dash' ],
                'type'                          => [ 'bail', 'required', 'string', 'in:product,order' ],
                'state'                         => [ 'bail', 'required', 'numeric', Rule::in([ 0, 1, 2 ]) ],
                'applied'                       => [ 'bail', 'required', 'array', 'size:6' ],
                'applied.allCustomer'           => [ 'bail', 'required', 'boolean' ],
                'applied.someSegment'           => [ 'bail', 'required', 'boolean' ],
                'applied.someCustomer'          => [ 'bail', 'required', 'boolean' ],
                'applied.allProduct'            => [ 'bail', 'required', 'boolean' ],
                'applied.someCategory'          => [ 'bail', 'required', 'boolean' ],
                'applied.someProduct'           => [ 'bail', 'required', 'boolean' ],
                'required_code'                 => [ 'bail', 'boolean', Rule::requiredIf($this->type === 'order') ],
                'rule'                          => [ 'bail', 'required', 'array', 'max:6', 'min:1' ],
                'rule.all'                      => [ 'bail', 'array', Rule::requiredIf($applied[ 'allProduct' ]) ],
                'rule.all.minimumQty'           => [ 'bail', 'required', 'numeric', 'min:1' ],
                'rule.all.discountValue'        => [ 'bail', 'required', 'numeric', 'min:1' ],
                'rule.all.discountType'         => [ 'bail', 'required', 'string', 'in:%,đ' ],
                'rule.category'                 => [ 'bail', 'array', Rule::requiredIf($applied[ 'someCategory' ]) ],
                'rule.category.*'               => [ 'bail', 'required', 'array', 'size:5' ],
                'rule.category.*.uuid'          => [ 'bail', 'required', 'string', 'size:21' ],
                'rule.category.*.name'          => [ 'bail', 'required', 'string', 'max:191' ],
                'rule.category.*.minimumQty'    => [ 'bail', 'required', 'numeric', 'min:1' ],
                'rule.category.*.discountValue' => [ 'bail', 'required', 'numeric', 'min:1' ],
                'rule.category.*.discountType'  => [ 'bail', 'required', 'string', 'in:%,đ' ],
                'rule.product'                  => [ 'bail', 'array', Rule::requiredIf($applied[ 'someProduct' ]) ],
                'rule.product.*'                => [ 'bail', 'required', 'array', 'size:6' ],
                'rule.product.*.uuid'           => [ 'bail', 'required', 'string', 'size:21' ],
                'rule.product.*.name'           => [ 'bail', 'required', 'string', 'max:191' ],
                'rule.product.*.price'          => [ 'bail', 'required', 'numeric', 'min:1' ],
                'rule.product.*.minimumQty'     => [ 'bail', 'required', 'numeric', 'min:1' ],
                'rule.product.*.discountValue'  => [
                    'bail',
                    'required',
                    'numeric',
                    'min:0',
                    'lte:rule.product.*.price',
                ],
                'rule.product.*.discountType'   => [ 'bail', 'required', 'string', 'in:%,đ' ],
                'rule.order'                    => [ 'bail', 'array', Rule::requiredIf($this->type === 'order') ],
                'rule.order.*'                  => [ 'bail', 'required', 'array' ],
                'rule.order.*.minimumPrice'     => [ 'bail', 'required', 'numeric', 'min:1' ],
                'rule.order.*.discountValue'    => [ 'bail', 'required', 'numeric', 'min:1' ],
                'rule.order.*.discountType'     => [ 'bail', 'required', 'string', 'in:%,đ' ],
                'customers'                     => [
                    'bail',
                    'array',
                    Rule::requiredIf(!$applied[ 'allCustomer' ] && empty($this->segments)),
                ],
                'customers.*'                   => [ 'bail', 'required', 'array', 'size:3' ],
                'customers.*.uuid'              => [ 'bail', 'required', 'string', 'size:21' ],
                'customers.*.name'              => [ 'bail', 'required', 'string', 'max:191' ],
                'customers.*.code'              => [ 'bail', 'required', 'string', 'alpha_num', 'max:191' ],
                'segments'                      => [
                    'bail',
                    'array',
                    Rule::requiredIf(!$applied[ 'allCustomer' ] && empty($this->customers)),
                ],
                'segments.*'                    => [ 'bail', 'required', 'array', 'size:2' ],
                'segments.*.uuid'               => [ 'bail', 'required', 'string', 'size:21' ],
                'segments.*.name'               => [ 'bail', 'required', 'string', 'max:191' ],
                'note'                          => [ 'bail', 'nullable', 'string', 'max:191' ],
                'is_limited'                    => [ 'bail', 'required', 'boolean' ],
                'limit_qty'                     => [
                    'bail',
                    'numeric',
                    Rule::requiredIf($isLimited),
                    function ($attribute, $value, $fail) use ($isLimited) {
                        if ( $isLimited && $value < 1 ) {
                            $fail('Số lượng giới hạn phải lớn hơn 0');
                        }
                    },
                ],
                'from'                          => [ 'bail', 'required', 'date' ],
                'to'                            => [ 'bail', 'nullable', 'date', 'after_or_equal:from' ],
            ];
        }
        return [];
    }
}
