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
        if ( $this->routeIs('pos.orders.update') ) {
            $countItems = empty($items = $this->items ?? []) ? 0 : count($items);
            return [
                // general info
                'total_eater'      => [
                    'bail',
                    'sometimes',
                    'numeric',
                ],
                'card_name' => [
                    'bail',
                    'sometimes',
                    'nullable',
                    'string',
                    'max:10',
                ],
                'note'             => [
                    'bail',
                    'sometimes',
                    'nullable',
                    'string',
                    'max:191',
                ],
                'customer_uuid'    => [
                    'bail',
                    'sometimes',
                    'nullable',
                    'string',
                    'size:21',
                    new ExistsThenBindVal(
                        \App\Models\Account::class,
                        'uuid',
                        'customer'
                    ),
                ],
                // items
                'items'            => [
                    'bail',
                    'sometimes',
                    'array',
                    'max:100',
                ],
                'items.*.uuid'     => [
                    'bail',
                    Rule::requiredIf($countItems),
                    'string',
                    'size:21',
                ],
                'items.*.quantity' => [
                    'bail',
                    Rule::requiredIf($countItems),
                    'numeric',
                    'min:1',
                    'max:999',
                ],
                'items.*.state'    => [
                    'bail',
                    Rule::requiredIf($countItems),
                    'numeric',
                    'min:0',
                    'max:6',
                ],
                'items.*.note'     => [
                    'bail',
                    'sometimes',
                    'string',
                    'max:191',
                ],
                'table_uuid'       => [
                    'bail',
                    'sometimes',
                    'nullable',
                    'string',
                    'size:21',
                    new ExistsThenBindVal(
                        \App\Models\Table::class,
                        'uuid',
                        'table'
                    ),
                ],
                // canceled
                'is_canceled'      => [
                    'sometimes',
                    'boolean',
                ],
                'reason'           => [
                    Rule::requiredIf($this->is_canceled ?? false),
                    'nullable',
                    'string',
                ],
                // payment
                'paid'             => [
                    'sometimes',
                    'numeric',
                ],
                'received_amount'  => [
                    'sometimes',
                    'numeric',
                ],
            ];
        }
        if ( $this->routeIs([ 'pos.orders.canceled' ]) ) {
            return [
                'is_canceled' => [
                    'bail',
                    'required',
                    'boolean',
                ],
                'reason'      => [
                    'bail',
                    Rule::requiredIf($this->is_canceled ?? false),
                    'string',
                    'max:191',
                ],
            ];
        }
        if ( $this->routeIs([ 'pos.orders.items' ]) ) {
            $countItems = empty($items = $this->items ?? []) ? 0 : count($items);
            return [
                'items'            => [
                    'bail',
                    'required',
                    'array',
                    'max:100',
                ],
                'items.*.uuid'     => [
                    'bail',
                    Rule::requiredIf($countItems),
                    'string',
                    'size:21',
                ],
                'items.*.quantity' => [
                    'bail',
                    Rule::requiredIf($countItems),
                    'numeric',
                    'min:1',
                ],
                'items.*.state'    => [
                    'bail',
                    Rule::requiredIf($countItems),
                    'numeric',
                    'min:0',
                    'max:6',
                ],
                'items.*.note'     => [
                    'bail',
                    'sometimes',
                    'nullable',
                    'string',
                    'max:191',
                ],
            ];
        }
        if ( $this->routeIs([ 'pos.orders.payment' ]) ) {
            return [
                'amount'          => [
                    'bail',
                    'required',
                    'numeric',
                ],
                'received_amount' => [
                    'bail',
                    'required',
                    'numeric',
                ],
            ];
        }
        if ( $this->routeIs([ 'pos.orders.table' ]) ) {
            return [
                'table_uuid' => [
                    'bail',
                    'required',
                    'string',
                    'size:21',
                    new ExistsThenBindVal(
                        \App\Models\Table::class,
                        'uuid',
                        'table'
                    ),
                ],
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
