<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Rules\ExistsThenBindVal;
use Illuminate\Foundation\Http\FormRequest;

class SupplyRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		if ($this->user()->hasAnyRole(['admin', 'superadmin']) || $this->user()->can('manage.products')) {
			return true;
		}
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		if ($this->routeIs(['supply.store', 'supply.update'])) {
			return [
				'name' => ['bail', 'required', 'string', 'max:191'],
				'unit_uuid' => [
					'bail',
					'string',
					'size:21',
					new ExistsThenBindVal(Category::class, 'uuid'),
				],
				'min_stock' => ['bail', 'numeric', 'max:9999999'],
			];
		}

		return [];
	}
}