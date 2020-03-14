<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\Category;
use App\Rules\GdExists;
use Illuminate\Foundation\Http\FormRequest;

class VoucherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ( $this->user()->hasAnyRole([ 'admin', 'superadmin' ])
            || $this->user()->can('cashflow.ledger') ) {
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
        if ( $this->routeIs([ 'voucher.store', 'voucher.update' ]) ) {
            return [
                'amount'           => [ 'bail', 'required', 'numeric', 'min:1' ],
                'imported_at'      => [ 'bail', 'required', 'date' ],
                'payment_method'   => 'required',
                'payer_payee_uuid' => [
                    'bail',
                    'required',
                    'string',
                    'size:21',
                    new GdExists(Account::class),
                ],
                'category_uuid'    => [
                    'bail',
                    'required',
                    'string',
                    'size:21',
                    new GdExists(Category::class),
                ],
            ];
        }

        if ( $this->routeIs([ 'voucher.index' ] && $this->category_uuid) ) {
            return [
                'category_uuid'    => [
                    'bail',
                    'string',
                    'size:21',
                    new ExistsThenBindVal(Category::class, 'uuid'),
                ],
            ];
        }
        return [];
    }
}
