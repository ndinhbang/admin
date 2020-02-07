<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource {
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function toArray($request) {
		return [
			'uuid' => isset($this->uuid) ? $this->uuid : null,
			'ref_code' => $this->ref_code,
			'total_price' => $this->total_price,
			'price_pu' => $this->price_pu,
			'qty_import' => $this->qty_import,
			'qty_export' => $this->qty_export,
			'qty_remain' => $this->qty_remain,
			'note' 		=> $this->note,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		];
	}
}
