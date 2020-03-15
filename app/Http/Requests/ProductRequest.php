<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Rules\GdExists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ( $this->user()->hasAnyRole([ 'admin', 'superadmin' ])
            || $this->user()->can('manage.products') ) {
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
        if ( $this->routeIs([ 'product.store', 'product.update' ]) ) {
            return [
                'price'               => [ 'bail', 'numeric' ],
                'price_sale'          => [ 'bail', 'nullable', 'sometimes', 'numeric' ],
                'name'                => [ 'bail', 'required', 'string', 'max:191' ],
                'is_hot'              => [ 'bail', 'boolean' ],
                'opened'              => [ 'bail', 'boolean' ],
                'can_stock'           => [ 'bail', 'boolean' ],
                'state'               => [ 'bail', 'boolean' ],
                'supplies'            => [ 'bail', 'array', 'max:25' ],
                'code'                => [ 'bail', 'nullable', 'sometimes', 'alpha_dash', 'max:20' ],
                'thumbnail'           => [ 'bail', 'nullable', 'string' ],
                'thumbnailFile'       => [
                    'bail',
                    'nullable',
                    'sometimes',
                    'image',
                    'mimes:jpeg,jpg,png,gif',
                    'max:2048',
                ],
                'category_uuid'       => [
                    'bail',
                    'required',
                    'string',
                    'size:21',
                    new GdExists(Category::class),
                ],
                'supplies.*.quantity' => [
                    'bail',
                    'numeric',
                    Rule::requiredIf(count($this->input('supplies')) > 0),
                ],
            ];
        }
        return [
            'is_hot' => [ 'bail', 'sometimes', 'boolean' ],
            'opened' => [ 'bail', 'sometimes', 'boolean' ],
            'state'  => [ 'bail', 'sometimes', 'boolean' ],
        ];
    }

    public function messages()
    {
        return [
            'category_uuid.required'       => 'Bạn chưa nhập danh mục cho sản phẩm ',
            'category_uuid.size'           => 'Danh mục sản phẩm không đúng định dạng ',
            'price.required'               => 'Bạn chưa nhập giá sản phẩm ',
            'price.numeric'                => 'Giá sản phẩm phải là số ',
            'name.max'                     => 'Tên sản phẩm vượt quá số kí tự cho phép (191 kí tự)',
            'name.required'                => 'Bạn chưa nhập tên sản phẩm ',
            'thumbnail.required'           => 'Bạn chưa chọn ảnh đại diện cho sản phẩm ',
            'thumbnail.max'                => 'Tên ảnh đại diện sản phẩm vượt quá dài',
            'supplies.*.id.required'       => 'Bạn chưa chon nguyện liệu ',
            'supplies.*.id.integer'        => 'ID nguyên liệu phải là số ',
            'supplies.*.quantity.required' => 'Bạn chưa nhập sô lượng nguyên liệu ',
            'supplies.*.quantity.numeric'  => 'Số lượng nguyên liệu phải là số',
            'thumbnail.image'              => 'Ảnh tải lên không đúng định dạng',
        ];
    }
}
