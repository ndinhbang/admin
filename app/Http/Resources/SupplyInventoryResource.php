<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplyInventoryResource extends JsonResource {
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function toArray($request) {
		return [
			'uuid' => $this->uuid,
			'name' => $this->name,
			'price_in' => $this->price_in,
			'remain_total' => $this->remain_total,
			// $this->mergeWhen($this->resource->relationLoaded('inventory'), [
			// 	'inventory' => InventoryResource::collection($this->inventory),
			// ]),
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		];
	}
}
