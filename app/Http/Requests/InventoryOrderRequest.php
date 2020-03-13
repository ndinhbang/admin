<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Rules\GdExists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->user()->hasAnyRole(['admin', 'superadmin']) || $this->user()->can('manage.inventory')) {
            return true;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \Exception
     */
    public function rules()
    {
        if ($this->routeIs(['inventory_order.store', 'inventory_order.update'])) {
            return [
                'amount'              => ['bail', 'required', 'numeric', 'min:1'],
                'supplies'            => ['bail', 'array', 'max:25'],
                'code'                => ['bail', 'nullable', 'sometimes', 'alpha_dash', 'max:20'],
                'on_date'             => ['bail', 'required', 'date'],
                'supplier_uuid'       => [
                    'bail',
                    'required',
                    'string',
                    'size:21',
                    new GdExists(Account::class),
                ],
                'supplies.*.qty_import' => [
                    'bail',
                    'numeric',
                    Rule::requiredIf(count($this->input('supplies')) > 0),
                ],
                'supplies.*.total_price' => [
                    'bail',
                    'numeric',
                    'min:1',
                    Rule::requiredIf(count($this->input('supplies')) > 0),
                ],
            ];
        }

        return [];
    }

    public function messages()
    {
        return [
            'supplier_uuid.required'       => 'Bạn chưa nhập nhà cung cấp',
            'category_uuid.size'           => 'Nhà cung cấp không đúng định dạng ',
            'amount.required'               => 'Bạn chưa chọn nguyên liệu hoặc chưa nhập giá của nguyên liệu',
            'amount.numeric'                => 'Giá nhập phải là số ',
            'amount.min'                    => 'Bạn chưa chọn nguyên liệu hoặc chưa nhập giá của nguyên liệu',
            'supplies.*.id.required'       => 'Bạn chưa chon nguyện liệu ',
            'supplies.*.id.integer'        => 'ID nguyên liệu phải là số ',
            'supplies.*.qty_import.required' => 'Bạn chưa nhập sô lượng nguyên liệu ',
            'supplies.*.qty_import.numeric'  => 'Số lượng nguyên liệu phải là số',
            'supplies.*.total_price.numeric'  => 'Giá nhập phải là số',
            'supplies.*.total_price.min'  => 'Bạn chưa nhập giá cho nguyên liệu',
        ];
    }
}
