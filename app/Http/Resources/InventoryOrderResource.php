<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InventoryOrderResource extends JsonResource {
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function toArray($request) {
		return [
			'uuid' => $this->uuid,
			'code' => $this->code,
			$this->mergeWhen($this->resource->relationLoaded('supplier'), [
				'supplier_uuid' => $this->supplier->uuid,
				'supplier_name' => $this->supplier->name,
				'supplier_code' => $this->supplier->code,
				'supplier_type' => $this->supplier->type,
			]),
			$this->mergeWhen($this->resource->relationLoaded('creator'), [
				'creator_uuid' => $this->creator->uuid,
				'creator_name' => $this->creator->display_name,
			]),
			'supplies' => InventorySupplyResource::collection($this->whenLoaded('supplies')),

			'on_date' => $this->on_date,
			'amount' => $this->amount,
			'debt' => $this->debt,
			'paid' => $this->paid,
			'status' => $this->status,
			'note' => $this->note,
			'type' => $this->type,
			'user_id' => $this->user_id,
			'updated_at' => $this->updated_at,
		];
	}
}
