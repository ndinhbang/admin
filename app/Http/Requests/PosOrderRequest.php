<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\Promotion;
use App\Models\Table;
use App\Rules\GdArrayExists;
use App\Rules\GdExists;
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
        if ( $this->user()->hasAnyRole(
                [ 'admin', 'superadmin' ]
            ) || $this->user()->can('pos') ) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \Exception
     */
    public function rules()
    {
        if ( $this->routeIs([ 'pos.orders.store', 'pos.orders.update' ]) ) {
            return [
                'total_eater'                     => [ 'bail', 'required', 'numeric', ],
                'total_dish'                      => [ 'bail', 'required', 'numeric' ],
                'amount'                          => [ 'bail', 'required', 'numeric', 'min:0' ],
                'discount_amount'                 => [ 'bail', 'required', 'numeric' ],
                'received_amount'                 => [ 'bail', 'required', 'numeric' ],
                'is_canceled'                     => [ 'bail', 'required', 'boolean' ],
                'is_returned'                     => [ 'bail', 'required', 'boolean' ],
                'is_served'                       => [ 'bail', 'required', 'boolean' ],
                'is_paid'                         => [ 'bail', 'required', 'boolean' ],
                'is_completed'                    => [ 'bail', 'required', 'boolean' ],
                'card_name'                       => [ 'bail', 'sometimes', 'nullable', 'string', 'max:10' ],
                'note'                            => [ 'bail', 'sometimes', 'nullable', 'string', 'max:191' ],
                'items'                           => [ 'bail', 'sometimes', 'array', 'max:100' ],
                'items.*.uuid'                    => [ 'bail', 'required', 'alpha_dash', 'size:21' ],
                'items.*.product_uuid'            => [ 'bail', 'required', 'alpha_dash', 'size:21' ],
                'items.*.quantity'                => [ 'bail', 'numeric', 'required', 'max:999' ],
                'items.*.added_qty'               => [ 'bail', 'required', 'numeric', 'max:999' ],
                'items.*.note'                    => [ 'bail', 'sometimes', 'string', 'max:191' ],
                'items.*.children'                => [ 'bail', 'sometimes', 'array', 'max:10' ],
                'items.*.children.*.uuid'         => [ 'bail', 'required', 'alpha_dash', 'size:21' ],
                'items.*.children.*.product_uuid' => [ 'bail', 'required', 'alpha_dash', 'size:21' ],
                'items.*.children.*.quantity'     => [ 'bail', 'required', 'numeric', 'max:999' ],
                'items.*.children.*.added_qty'    => [ 'bail', 'required', 'numeric', 'max:999' ],
                'items.*.children.*.note'         => [ 'bail', 'sometimes', 'string', 'max:191' ],
                'customer_uuid'                   => [
                    'bail',
                    'sometimes',
                    'nullable',
                    'alpha_dash',
                    'size:21',
                    ( new GdExists(Account::class, 'uuid', '__customer') ),
                ],
                'table_uuid'                      => [
                    'bail',
                    'sometimes',
                    'nullable',
                    'alpha_dash',
                    'size:21',
                    new GdExists(Table::class),
                ],
                'promotion_uuid'                  => [ 'bail', 'sometimes', 'nullable', 'alpha_dash', 'size:21' ],
//                'promotion_applied'                   => [ 'bail', 'sometimes', 'nullable', 'array' ],
//                'promotion_applied.type'              => [
//                    'bail',
//                    'in:order,product',
//                    Rule::requiredIf($hasPromotionApplied),
//                ],
//                'promotion_applied.code'              => [
//                    'bail',
//                    'string',
//                    'max:191',
//                    Rule::requiredIf($hasPromotionApplied),
//                ],
//                'promotion_applied.discountAmount'    => [
//                    'bail',
//                    'numeric',
//                    'min:1',
//                    Rule::requiredIf($hasPromotionApplied),
//                ],
                'promotions'                      => [
                    'bail',
                    'sometimes',
                    'array',
                    new GdArrayExists(Promotion::class, '__keyedPromotions'),
                ],
                'promotions.*.uuid'               => [
                    'bail',
                    'alpha_dash',
                    'size:21',
                    Rule::requiredIf($this->promotions ?? false),
                ],
                'promotions.*.discount_amount'    => [
                    'required',
                    'numeric',
                    'min:1',
                    Rule::requiredIf($this->promotions ?? false),
                ],
            ];
        }
        if ( $this->routeIs([ 'pos.orders.canceled' ]) ) {
            return [
                'reason' => [ 'bail', 'max:191' ],
            ];
        }
        if ( $this->routeIs([ 'pos.orders.index' ]) ) {
            return [
                'day' => [ 'bail', 'required' ],
            ];
        }
        return [
            'kind' => [
                'sometimes',
                'numeric',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'amount'                           => 'Tiền thanh toán',
            'card_name'                        => 'Thẻ tên',
            'note'                             => 'Ghi chú',
            'promotion_applied.type'           => 'Kiểu khuyến mãi',
            'promotion_applied.code'           => 'Mã khuyến mãi',
            'promotion_applied.discountAmount' => 'Giá trị khuyến mãi',
            'reason'                           => 'Lý do',
        ];
    }

    public function messages()
    {
        return [
            'items.max'                   => 'Tối đa :max sản phẩm / 1 đơn hàng',
            'items.*.children.max'        => 'Tối đa :max sản phẩm bán kèm / 1 sản phẩm',
            'items.*.children.*.quantity' => 'Số lượng sản phẩm tối đa :max',
            'items.*.quantity.max'        => 'Số lượng sản phẩm tối đa :max',
        ];
    }
}
