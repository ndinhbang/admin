<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource {
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function toArray($request) {
		$stateArr = config('default.orders.state');

		return [
			'uuid' => $this->uuid,
			'code' => $this->code,
			'card_name' => $this->card_name,
			'kind' => $this->kind,
			'state' => $this->state,
			'state_name' => $stateArr[$this->state ?? 0]['name'],
			'amount' => $this->amount,
			'debt' => $this->debt,
			'paid' => $this->paid,
			'discount_amount' => $this->discount_amount,
			'discount_items_amount' => $this->discount_items_amount,
			'received_amount' => $this->received_amount,
			'is_returned' => $this->is_returned,
			'is_canceled' => $this->is_canceled,
			'is_served' => $this->is_served,
			'is_paid' => $this->is_paid,
			'is_completed' => $this->is_completed,
			'note' => $this->note,
			'reason' => $this->reason,
			'total_dish' => $this->total_dish,
			'total_eater' => $this->total_eater,
			'created_at' => $this->created_at,
			'newItems' => [],
			'items' => $this->resource->relationLoaded('items')
			? PosProductResource::collection($this->items)
			: [],
			$this->mergeWhen($this->resource->relationLoaded('table'), [
				'table_uuid' => $this->table->uuid ?? '',
				'table' => $this->table,
			]),
			$this->mergeWhen($this->resource->relationLoaded('customer'), [
				'customer_uuid' => $this->customer->uuid ?? '',
				'customer' => $this->customer,
			]),
			$this->mergeWhen($this->resource->relationLoaded('creator'), [
				'creator_uuid' => $this->creator->uuid ?? '',
				'creator' => $this->creator,
			]),
		];
	}
}
