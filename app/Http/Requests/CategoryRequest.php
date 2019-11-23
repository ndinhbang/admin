<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
                ]) || $this->user()
                ->can('manage.categories') ) {
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
//		if ($this->routeIs(['category.store', 'category.update'])) {
//			return [
//				'type' => 'sometimes|in:menu,unit,revenue,expense',
//				'state' => 'sometimes|boolean',
//			];
//		}
        return [
            'type' => 'sometimes|in:menu,unit,revenue,expense',
            'state' => 'sometimes|boolean',
        ];
    }
}
