<?php

namespace App\Http\Resources;

use App\Traits\UsingAdditionalData;
use Illuminate\Http\Resources\Json\JsonResource;

class PosOrderResource extends JsonResource
{
    use UsingAdditionalData;

    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request  $request
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
            'discount_value'  => $this->discount_amount,
            'discount_type'   => 'đ',
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
            'updated_at'      => $this->updated_at,
            'stage'           => 'remote',
            '$isDirty'        => false,
            '$isNew'          => false,
            'items'           => ( new OrderItemsCollection($this->whenLoaded('items')) )->using([
                'parent_uuid' => null,
            ]),
            'place_uuid'      => $this->whenLoaded('place', function () {
                return $this->place->uuid;
            }),
            $this->mergeWhen($this->resource->relationLoaded('table'), function () {
                return [
                    'table_uuid' => $this->table->uuid ?? null,
                    'table_name' => $this->table->name ?? '',
                    'table'      => new TableResource($this->table),
                ];
            }),
            $this->mergeWhen($this->resource->relationLoaded('customer'), function () {
                return [
                    'customer_uuid' => $this->customer->uuid ?? null,
                    'customer_name' => $this->customer->name ?? '',
                    'customer_code' => $this->customer->code ?? '',
                    'customer'      => $this->customer,
                ];
            }),
            $this->merge($this->using),
        ];
    }
}
