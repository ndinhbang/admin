<?php

namespace App\Http\Requests;

use App\Models\OrderItem;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ExistsThenBindVal;

class PrintRequest extends FormRequest
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
        $templates = config('default.print.templates');
        return [
            'template' => [
                'bail',
                'nullable',
                'sometimes',
                'string',
                'in:' . implode(',', (array) $templates),
            ],
            'item_id'  => [
                'bail',
                'nullable',
                'sometimes',
                'numeric',
                new ExistsThenBindVal(OrderItem::class, 'id', 'orderItem'),
            ],
            'stt'      => [
                'bail',
                'required_with:item_id',
                'numeric',
            ],
        ];
    }
}
