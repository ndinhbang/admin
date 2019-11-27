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
            $countBatchItems = empty($batchItems = $this->batchItems ?? []) ? 0 : count($batchItems);
            return [
                // general info
                'total_eater'      => [ 'bail', 'sometimes', 'numeric'],
                'card_name'        => [ 'bail', 'sometimes', 'nullable', 'string', 'max:10'],
                'note'             => [ 'bail', 'sometimes', 'nullable', 'string', 'max:191'],
                'customer_uuid'    => [
                    'bail', 'sometimes', 'nullable', 'alpha_dash', 'size:21',
                    new ExistsThenBindVal(
                        \App\Models\Account::class,
                        'uuid',
                        'orderCustomer'
                    ),
                ],
                'items'            => [ 'bail', 'sometimes', 'array', 'max:100'],
                'items.*.uuid'     => [
                    'bail', 'alpha_dash', 'size:21',
                    Rule::requiredIf($countItems),
                ],
                'items.*.quantity' => [
                    'bail', 'numeric', 'min:1', 'max:255',
                    Rule::requiredIf($countItems),
                ],
                'items.*.note'     => [ 'bail', 'sometimes', 'string', 'max:191'],
                'batchItems'            => [
                    'bail', 'sometimes', 'array', 'max:100',
                    // max batch items = items
                    function ($attribute, $value, $fail)
                        use ($countItems, $countBatchItems) {
                            if ($countBatchItems > $countItems) {
                                $fail($attribute.' is invalid.');
                            }
                    },
                ],
                'batchItems.*.uuid'     => [
                    'bail', 'alpha_dash', 'size:21',
                    Rule::requiredIf($countBatchItems),
                ],
                'batchItems.*.quantity' => [
                    'bail', 'numeric', 'min:1', 'max:255',
                    Rule::requiredIf($countBatchItems),
                ],
                'batchItems.*.note'     => [ 'bail', 'sometimes', 'string', 'max:191'],
                'table_uuid' => [
                    'bail', 'sometimes', 'nullable', 'alpha_dash', 'size:21',
                    new ExistsThenBindVal(
                        \App\Models\Table::class,
                        'uuid',
                        'orderTable'
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
        if ( $this->routeIs([ 'pos.orders.payment' ]) ) {
            return [
                'amount'          => [ 'bail', 'required', 'numeric'],
                'received_amount' => [ 'bail', 'required', 'numeric'],
            ];
        }

        if ( $this->routeIs([ 'pos.orders.payment' ]) ) {
            return [
//                'amount'          => [ 'bail', 'required', 'numeric'],
                'received_amount' => [ 'bail', 'required', 'numeric'],
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
