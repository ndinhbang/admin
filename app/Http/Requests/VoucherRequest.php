<?php

namespace App\Http\Requests;

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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->routeIs('voucher.store')) {
            return [
                'amount'   => 'required|numeric',
                'imported_at'   => 'required',
                'payment_method' => 'required',
                'category' => 'required',
            ];
        }

        return [];
    }
}
