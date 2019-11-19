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
			'code' => $this->code,
			'on_date' => $this->on_date,
			'amount' => $this->amount,
			'debt' => $this->debt,
			'paid' => $this->paid,
			'status' => $this->status,
			'note' => $this->note,
			'type' => $this->type,
			'supply_quantity' => $this->pivot_quantity,
			'supply_price_pu' => $this->pivot_price_pu,
			'supply_remain' => $this->pivot_remain,
			'supply_total_price' => $this->pivot_total_price,
			'supplier_name' => $this->supplier_name,
			'creator_name' => $this->creator_name,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		];
	}
}
