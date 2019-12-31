<?php

namespace App\Http\Requests;

use App\Rules\ExistsThenBindVal;
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
        if ( $this->user()
                ->hasAnyRole([
                    'admin',
                    'superadmin',
                ])
            || $this->user()
                ->can('pos') ) {
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
        if ( $this->routeIs(['pos.orders.store', 'pos.orders.update']) ) {
            $countItems = empty($items = $this->items ?? []) ? 0 : count($items);
            return [
                'total_eater'      => [ 'bail', 'required', 'numeric'],
                'total_dish'       => [ 'bail', 'required', 'numeric'],
                'amount'           => [ 'bail', 'required', 'numeric'],
                'discount_amount'  => [ 'bail', 'required', 'numeric'],
                'received_amount'  => [ 'bail', 'required', 'numeric'],
                'is_canceled'      => [ 'bail', 'required', 'boolean'],
                'is_returned'      => [ 'bail', 'required', 'boolean'],
                'is_served'        => [ 'bail', 'required', 'boolean'],
                'is_paid'          => [ 'bail', 'required', 'boolean'],
                'is_completed'     => [ 'bail', 'required', 'boolean'],
                'card_name'        => [ 'bail', 'sometimes', 'nullable', 'string', 'max:10'],
                'note'             => [ 'bail', 'sometimes', 'nullable', 'string', 'max:191'],
                'items'            => [ 'bail', 'required', 'array', 'max:100'],
                'items.*.uuid'     => [
                    'bail', 'alpha_dash', 'size:21',
                    Rule::requiredIf($countItems),
                ],
                // product
                'items.*.product_uuid'     => [
                    'bail', 'alpha_dash', 'size:21',
                    Rule::requiredIf($countItems),
                ],
                'items.*.quantity' => [
                    'bail', 'numeric', 'min:1', 'max:255',
                    Rule::requiredIf($countItems),
                ],
                'items.*.note'     => [ 'bail', 'sometimes', 'string', 'max:191'],
                // child items
                'items.*.children'                      => [ 'bail', 'sometimes', 'array', 'max:10'],
                'items.*.children.*.uuid'               => [ 'bail', 'sometimes', 'alpha_dash', 'size:21'],
                'items.*.children.*.product_uuid'       => [ 'bail', 'sometimes', 'alpha_dash', 'size:21'],
                'items.*.children.*.quantity'           => [ 'bail', 'sometimes', 'numeric', 'min:1', 'max:255'],
                'customer_uuid'    => [
                    'bail', 'sometimes', 'nullable', 'alpha_dash', 'size:21',
                    new ExistsThenBindVal(
                        \App\Models\Account::class,
                        'uuid',
                        '__customer'
                    ),
                ],
                'table_uuid' => [
                    'bail', 'sometimes', 'nullable', 'alpha_dash', 'size:21',
                    new ExistsThenBindVal(
                        \App\Models\Table::class,
                        'uuid',
                        '__table'
                    ),
                ],
            ];
        }
        if ( $this->routeIs([ 'pos.orders.canceled' ]) ) {
            return [
                'is_canceled' => [ 'bail', 'required', 'boolean'],
                'reason'      => [
                    'bail', 'string', 'max:191',
                    Rule::requiredIf($this->is_canceled ?? false),
                ],
            ];
        }

        if ( $this->routeIs([ 'pos.orders.index' ]) ) {
            return [
                'day'          => [ 'bail', 'required'],
            ];
        }

        return [
            'kind' => [ 'sometimes', 'numeric'],
        ];
    }
}
