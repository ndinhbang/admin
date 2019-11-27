<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed customer
 * @property mixed table
 * @property mixed created_at
 * @property mixed total_dish
 * @property mixed note
 * @property mixed is_completed
 * @property mixed is_paid
 * @property mixed is_served
 * @property mixed is_canceled
 * @property mixed is_returned
 * @property mixed received_amount
 * @property mixed paid
 * @property mixed debt
 * @property mixed amount
 * @property mixed state
 * @property mixed code
 * @property mixed uuid
 * @property mixed reason
 * @property mixed total_eater
 * @property mixed card_name
 * @property mixed kind
 */
class PosOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $stateArr = config('default.orders.state');
        $items    = $this->resource->relationLoaded('items')
            ? PosProductResource::collection($this->items)
            : [];
        return [
            'uuid'            => $this->uuid,
            'code'            => $this->code,
            'card_name'       => $this->card_name,
            'kind'            => $this->kind,
            'state'           => $this->state,
            'state_name'      => $stateArr[ $this->state ?? 0 ]['name'],
            'amount'          => $this->amount,
            'debt'            => $this->debt,
            'paid'            => $this->paid,
            'received_amount' => $this->received_amount,
            'is_returned'     => $this->is_returned,
            'is_canceled'     => $this->is_canceled,
            'is_served'       => $this->is_served,
            'is_paid'         => $this->is_paid,
            'is_completed'    => $this->is_completed,
            'note'            => $this->note,
            'reason'          => $this->reason,
            'total_dish'      => $this->total_dish,
            'total_eater'     => $this->total_eater,
            'created_at'      => $this->created_at,
            'batchItems'      => [],
//            'originItems'     => $items,
            'items'           => $items,
            $this->mergeWhen($this->resource->relationLoaded('table'), [
                'table_uuid' => $this->table->uuid ?? '',
                'table'      => new TableResource($this->table),
            ]),
            $this->mergeWhen($this->resource->relationLoaded('customer'), [
                'customer_uuid' => $this->customer->uuid ?? '',
                'customer'      => $this->customer,
            ]),
        ];
    }
}
