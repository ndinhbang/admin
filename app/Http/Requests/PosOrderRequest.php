<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\Table;
use App\Rules\ExistsThenBindVal;
use Illuminate\Foundation\Http\FormRequest;

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
     */
    public function rules()
    {
        if ( $this->routeIs([ 'pos.orders.store', 'pos.orders.update' ]) ) {
            return [
                'total_eater'                     => [ 'bail', 'required', 'numeric', ],
                'total_dish'                      => [ 'bail', 'required', 'numeric' ],
                'amount'                          => [ 'bail', 'required', 'numeric' ],
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
                'items.*.uuid'                    => [ 'bail', 'alpha_dash', 'size:21', 'required' ],
                'items.*.product_uuid'            => [ 'bail', 'alpha_dash', 'size:21', 'required' ],
                'items.*.quantity'                => [ 'bail', 'numeric', 'required' ],
                'items.*.added_qty'               => [ 'bail', 'required', 'numeric', 'min:0', 'max:255' ],
                'items.*.note'                    => [ 'bail', 'sometimes', 'string', 'max:191' ],
                'items.*.children'                => [ 'bail', 'sometimes', 'array', 'max:10' ],
                'items.*.children.*.uuid'         => [ 'bail', 'required', 'alpha_dash', 'size:21' ],
                'items.*.children.*.product_uuid' => [ 'bail', 'required', 'alpha_dash', 'size:21' ],
                'items.*.children.*.quantity'     => [ 'bail', 'required', 'numeric', 'min:1', 'max:255' ],
                'items.*.children.*.added_qty'    => [ 'bail', 'required', 'numeric', 'min:0', 'max:255' ],
                'items.*.children.*.note'         => [ 'bail', 'sometimes', 'string', 'max:191' ],
                'customer_uuid'                   => [
                    'bail',
                    'sometimes',
                    'nullable',
                    'alpha_dash',
                    'size:21',
                    new ExistsThenBindVal(Account::class, 'uuid', '__customer'),
                ],
                'table_uuid'                      => [
                    'bail',
                    'sometimes',
                    'nullable',
                    'alpha_dash',
                    'size:21',
                    new ExistsThenBindVal(Table::class, 'uuid', '__table'),
                ],
                'promotion_uuid' => ['bail', 'sometimes', 'nullable', 'string', 'size:21'],
                'applied_promotion' => [ 'bail', 'sometimes', 'nullable', 'array' ],
                'applied_promotion.type' => ['bail', 'required', 'in:order,product'],
                'applied_promotion.code' => ['bail', 'required', 'string'],
                'applied_promotion.discountAmount' => ['bail', 'required', 'numeric', 'min:1'],
                'applied_promotion.promotions' =>  ['bail', 'required', 'array', 'min:1'],
                'applied_promotion.promotions.*.uuid' =>  ['bail', 'required', 'string', 'size:21'],
                'applied_promotion.promotions.*.code' =>  ['bail', 'required', 'string', 'max:191'],
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
}
