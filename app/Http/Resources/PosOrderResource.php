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
     * @param    \Illuminate\Http\Request    $request
     * @return array
     * @throws \Exception
     */
    public function toArray( $request )
    {
        $stateArr = config('default.orders.state');
        return [
            'uuid'            => $this->uuid,
            'code'            => $this->code,
            'card_name'       => $this->card_name,
            'kind'            => getOrderKind($this->kind),
            'state'           => $this->state,
            'state_name'      => $stateArr[ $this->state ?? 0 ]['name'],
            'amount'          => $this->amount,
            'debt'            => $this->debt,
            'paid'            => $this->paid,
            'discount_amount' => $this->discount_amount,
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
            'stage'           => 'remote',
            '$isDirty'        => false,
            'items'           => OrderItemResource::collection($this->whenLoaded('items')),
            'place_uuid' => $this->whenLoaded('place', $this->place->uuid),
            $this->mergeWhen($this->whenLoaded('table'), [
                'table_uuid' => $this->table->uuid ?? '',
                'table'      => new TableResource($this->table),
            ]),
            $this->mergeWhen($this->whenLoaded('items'), function () {
                    $has_printed_qty = 0;
                    $items_printed_qty = [];
                    foreach ($this->items as $key => $item) {
                        if($item->added_qty) {
                            $has_printed_qty += $item->added_qty;
                            $items_printed_qty[] = $item;
                        }
                    }
                    return [
                        'has_printed_qty' => $has_printed_qty,
                        'items_printed_qty' => $items_printed_qty ?? [],
                    ];
            }),
            $this->mergeWhen($this->whenLoaded('customer'), [
                'customer_uuid' => $this->customer->uuid ?? '',
                'customer'      => $this->customer,
            ]),
        ];
    }
}
