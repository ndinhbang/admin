<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\Promotion;
use App\Models\Table;
use App\Rules\GdArrayExists;
use App\Rules\GdExists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
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
            $hasPromotionApplied = Arr::has($this->all(), 'promotion_applied.promotions');
            return [
                'total_eater'                         => [ 'bail', 'required', 'numeric', ],
                'total_dish'                          => [ 'bail', 'required', 'numeric' ],
                'amount'                              => [ 'bail', 'required', 'numeric' ],
                'discount_amount'                     => [ 'bail', 'required', 'numeric' ],
                'received_amount'                     => [ 'bail', 'required', 'numeric' ],
                'is_canceled'                         => [ 'bail', 'required', 'boolean' ],
                'is_returned'                         => [ 'bail', 'required', 'boolean' ],
                'is_served'                           => [ 'bail', 'required', 'boolean' ],
                'is_paid'                             => [ 'bail', 'required', 'boolean' ],
                'is_completed'                        => [ 'bail', 'required', 'boolean' ],
                'card_name'                           => [ 'bail', 'sometimes', 'nullable', 'string', 'max:10' ],
                'note'                                => [ 'bail', 'sometimes', 'nullable', 'string', 'max:191' ],
                'items'                               => [ 'bail', 'sometimes', 'array', 'max:100' ],
                'items.*.uuid'                        => [ 'bail', 'alpha_dash', 'size:21', 'required' ],
                'items.*.product_uuid'                => [ 'bail', 'alpha_dash', 'size:21', 'required' ],
                'items.*.quantity'                    => [ 'bail', 'numeric', 'required' ],
                'items.*.added_qty'                   => [ 'bail', 'required', 'numeric', 'min:0', 'max:255' ],
                'items.*.note'                        => [ 'bail', 'sometimes', 'string', 'max:191' ],
                'items.*.children'                    => [ 'bail', 'sometimes', 'array', 'max:10' ],
                'items.*.children.*.uuid'             => [ 'bail', 'required', 'alpha_dash', 'size:21' ],
                'items.*.children.*.product_uuid'     => [ 'bail', 'required', 'alpha_dash', 'size:21' ],
                'items.*.children.*.quantity'         => [ 'bail', 'required', 'numeric', 'min:1', 'max:255' ],
                'items.*.children.*.added_qty'        => [ 'bail', 'required', 'numeric', 'min:0', 'max:255' ],
                'items.*.children.*.note'             => [ 'bail', 'sometimes', 'string', 'max:191' ],
                'customer_uuid'                       => [
                    'bail',
                    'sometimes',
                    'nullable',
                    'alpha_dash',
                    'size:21',
                    new GdExists(Account::class),
                ],
                'table_uuid'                          => [
                    'bail',
                    'sometimes',
                    'nullable',
                    'alpha_dash',
                    'size:21',
                    new GdExists(Table::class),
                ],
                'promotion_uuid'                      => [ 'bail', 'sometimes', 'nullable', 'string', 'size:21' ],
                'promotion_applied'                   => [ 'bail', 'sometimes', 'nullable', 'array' ],
                'promotion_applied.type'              => [
                    'bail',
                    'in:order,product',
                    Rule::requiredIf($hasPromotionApplied),
                ],
                'promotion_applied.code'              => [ 'bail', 'string', Rule::requiredIf($hasPromotionApplied) ],
                'promotion_applied.discountAmount'    => [
                    'bail',
                    'numeric',
                    'min:1',
                    Rule::requiredIf($hasPromotionApplied),
                ],
                'promotion_applied.promotions'        => [
                    'bail',
                    'array',
                    'min:1',
                    Rule::requiredIf($hasPromotionApplied),
                    new GdArrayExists(Promotion::class, '__keyedPromotions'),
                ],
                'promotion_applied.promotions.*.uuid' => [
                    'bail',
                    'string',
                    'size:21',
                    Rule::requiredIf($hasPromotionApplied),
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
}
