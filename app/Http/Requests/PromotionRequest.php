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
        if (!$this->routeIs([ 'promotion.store', 'promotion.update' ])) {
            return true;
        }
        if ( $this->user()->hasAnyRole([ 'admin', 'superadmin' ])
            || $this->user()->can('manage.promotions') ) {
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
        if ( $this->routeIs([ 'promotion.store', 'promotion.update' ]) ) {
            $applied   = $this->input('applied');
            $isLimited = $this->input('is_limited', false);
            return [
                'name'                          => [ 'bail', 'required', 'string', 'max:50' ],
                'code'                          => [ 'bail', 'nullable', 'string', 'max:50', 'alpha_dash' ],
                'type'                          => [ 'bail', 'required', 'string', 'in:product,order' ],
                'state'                         => [ 'bail', 'required', 'numeric', Rule::in([ 0, 1, 2 ]) ],
                'applied'                       => [ 'bail', 'required', 'array' ],
                'applied.allCustomer'           => [ 'bail', 'required', 'boolean' ],
                'applied.someSegment'           => [ 'bail', 'required', 'boolean' ],
                'applied.someCustomer'          => [ 'bail', 'required', 'boolean' ],
                'applied.allProduct'            => [ 'bail', 'required', 'boolean' ],
                'applied.someCategory'          => [ 'bail', 'required', 'boolean' ],
                'applied.someProduct'           => [ 'bail', 'required', 'boolean' ],
                'applied.orderKind'             => [ 'bail', 'sometimes', 'in:all,inplace,takeaway' ],
                'required_code'                 => [ 'bail', 'boolean', Rule::requiredIf($this->type === 'order') ],
                'from'                          => [ 'bail', 'required', 'date' ],
                'to'                            => [ 'bail', 'nullable', 'date', 'after_or_equal:from' ],
                'rule.category'                 => [
                    'bail',
                    'array',
                    Rule::requiredIf($this->type === 'product' && $applied[ 'someCategory' ]),
                ],
                'rule.category.*'               => [ 'bail', 'required', 'array' ],
                'rule.category.*.uuid'          => [ 'bail', 'required', 'alpha_dash', 'size:21' ],
                'rule.category.*.name'          => [ 'bail', 'required', 'string', 'max:191' ],
                'rule.category.*.minimumQty'    => [ 'bail', 'required', 'numeric', 'min:1' ],
                'rule.category.*.discountValue' => [ 'bail', 'required', 'numeric', 'min:1' ],
                'rule.category.*.discountType'  => [ 'bail', 'required', 'string', 'in:%,đ' ],
                'rule.product'                  => [
                    'bail',
                    'array',
                    Rule::requiredIf($this->type === 'product' && $applied[ 'someProduct' ]),
                ],
                'rule.product.*'                => [ 'bail', 'required', 'array', 'size:6' ],
                'rule.product.*.uuid'           => [ 'bail', 'required', 'alpha_dash', 'size:21' ],
                'rule.product.*.name'           => [ 'bail', 'required', 'string', 'max:191' ],
                'rule.product.*.price'          => [ 'bail', 'required', 'numeric', 'min:1' ],
                'rule.product.*.minimumQty'     => [ 'bail', 'required', 'numeric', 'min:1' ],
                'rule.product.*.discountValue'  => [
                    'bail',
                    'required',
                    'numeric',
                    'min:1',
                    'lte:rule.product.*.price',
                ],
                'rule.product.*.discountType'   => [ 'bail', 'required', 'string', 'in:%,đ' ],
                'rule.order'                    => [ 'bail', 'array', Rule::requiredIf($this->type === 'order') ],
                'rule.order.*'                  => [ 'bail', 'required', 'array', 'size:3' ],
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
                'segments.*'                    => [ 'bail', 'required', 'array' ],
                'segments.*.uuid'               => [ 'bail', 'required', 'string', 'size:21' ],
                'segments.*.name'               => [ 'bail', 'required', 'string', 'max:191' ],
                'rule'                          => [ 'bail', 'required', 'array', 'max:6', 'min:1' ],
                'rule.all'                      => [
                    'bail',
                    'array',
                    Rule::requiredIf($applied[ 'allProduct' ] && $this->type === 'product'),
                ],
                'rule.all.minimumQty'           => [
                    'bail',
                    'numeric',
                    'min:1',
                    Rule::requiredIf($applied[ 'allProduct' ] && $this->type === 'product'),
                ],
                'rule.all.discountValue'        => [
                    'bail',
                    'numeric',
                    'min:1',
                    Rule::requiredIf($applied[ 'allProduct' ] && $this->type === 'product'),
                ],
                'rule.all.discountType'         => [
                    'bail',
                    'string',
                    'in:%,đ',
                ],
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
            ];
        }
        return [];
    }

    public function attributes()
    {
        return [
            'name'                   => 'Tên khuyến mãi',
            'code'                   => 'Mã khuyến mãi',
            'type'                   => 'Loại hình áp dụng KM',
            'from'                   => 'Ngày bắt đầu KM',
            'to'                     => 'Ngày kết thúc KM',
            'rule.all.minimumQty'    => 'Số lượng tối thiểu',
            'rule.all.discountValue' => 'Giảm giá',
            'note'                   => 'Ghi chú',
        ];
    }

    public function messages()
    {
        return [
            'rule.category.required' => 'Bạn chưa tạo điều kiện khuyến mãi',
            'rule.order.required'    => 'Bạn chưa tạo điều kiện khuyến mãi',
            'rule.product.*.size'    => 'Bạn chưa nhập đầy đủ thông tin cho điều kiện khuyến mãi',
            'rule.order.*.size'      => 'Bạn chưa nhập đầy đủ thông tin cho điều kiện khuyến mãi',
            'rule.category.*.size'   => 'Bạn chưa nhập đầy đủ thông tin cho điều kiện khuyến mãi',
            'rule.product.required'  => 'Bạn chưa tạo điều kiện khuyến mãi',
            'customers.required'     => 'Bạn chưa nhập đối tượng KM',
            'segments.required'      => 'Bạn chưa nhập đối tượng KM',
            'rule.all.required'      => 'Bạn chưa nhập điều kiện khuyến mãi',
        ];
    }
}
