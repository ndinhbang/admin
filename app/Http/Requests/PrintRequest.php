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
        if ($this->routeIs(['config.print'])) {
            return [
                'config'   => 'required|array|size:4',
                'config.print_both' => 'required|boolean',
                'config.print_draft' => 'required|boolean',
                'config.print_when_accepted' => 'required|boolean',
                'config.print_when_paid' => 'required|boolean',
            ];
        }

        if ($this->routeIs('config.printers')) {
            return [
                'printers'   => 'required|array|min:1',
                'printers.*.name'   => 'required|string|min:1',
                'printers.*.enable'   => 'required|boolean',
            ];
        }

        $templates = config('default.print.templates');

        return [
            'template' => [
                'bail','nullable','sometimes','string','in:' . implode(',', (array) $templates)
            ],
            'item_id' => [
                'bail','nullable','sometimes','numeric',
                new ExistsThenBindVal(OrderItem::class, 'id', 'orderItem'),
            ],
            'stt' => [
                'bail','required_with:item_id','numeric'
            ]
        ];
    }
}
