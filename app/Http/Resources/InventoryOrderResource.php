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
			'uuid' => isset($this->uuid) ? $this->uuid : null,
			'code' => $this->code,
			$this->mergeWhen($this->resource->relationLoaded('supplier'), [
				'supplier_uuid' => isset($this->supplier->uuid) ? $this->supplier->uuid : null,
				'supplier_name' => isset($this->supplier->name) ? $this->supplier->name : null,
				'supplier_code' => isset($this->supplier->code) ? $this->supplier->code : null,
				'supplier_type' => isset($this->supplier->type) ? $this->supplier->type : null,
				'supplier' => $this->supplier,
				
			]),
			$this->mergeWhen($this->resource->relationLoaded('creator'), [
				'creator_uuid' => isset($this->creator->uuid) ? $this->creator->uuid : null,
				'creator_name' => isset($this->creator->display_name) ? $this->creator->display_name : null,
			]),
			'supplies' => InventorySupplyResource::collection($this->whenLoaded('supplies')),

			'vouchers' => VoucherResource::collection($this->whenLoaded('vouchers')),

			'on_date' => $this->on_date,
			'payment_method' => $this->payment_method,
			'amount' => $this->amount,
			'debt' => $this->debt,
			'paid' => $this->paid,
			'status' => $this->status,
			'note' => $this->note,
			'type' => $this->type,
			'user_id' => $this->user_id,
			'updated_at' => $this->updated_at,
			'deleted_at' => $this->deleted_at,
		];
	}
}
