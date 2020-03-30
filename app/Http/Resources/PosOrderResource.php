<?php

namespace App\Http\Resources;

use App\Traits\UsingAdditionalData;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed uuid
 * @property mixed code
 * @property mixed card_name
 * @property mixed kind
 * @property mixed state
 * @property mixed amount
 * @property mixed debt
 * @property mixed paid
 * @property mixed promotion_uuid
 * @property mixed promotion_applied
 * @property mixed discount_amount
 * @property mixed received_amount
 * @property mixed is_returned
 * @property mixed is_canceled
 * @property mixed is_served
 * @property mixed is_paid
 * @property mixed is_completed
 * @property mixed note
 * @property mixed reason
 * @property mixed total_dish
 * @property mixed total_eater
 * @property mixed created_at
 * @property mixed updated_at
 * @property mixed promotion_automated
 */
class PosOrderResource extends JsonResource
{
    use UsingAdditionalData;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     * @throws \Exception
     */
    public function toArray($request)
    {
        $stateArr = config('default.orders.state');
        return [
            'uuid'                => $this->uuid,
            'code'                => $this->code,
            'card_name'           => $this->card_name,
            'kind'                => getOrderKind($this->kind),
            'state'               => $this->state,
            'state_name'          => $stateArr[ $this->state ?? 0 ][ 'name' ],
            'amount'              => $this->amount,
            'debt'                => $this->debt,
            'paid'                => $this->paid,
            'promotion_uuid'      => $this->promotion_uuid,
            'promotion_automated' => $this->promotion_automated,
            'discount_amount'     => $this->discount_amount,
            'discount_value'      => $this->discount_amount,
            'discount_type'       => 'đ',
            'received_amount'     => $this->received_amount,
            'is_returned'         => $this->is_returned,
            'is_canceled'         => $this->is_canceled,
            'is_served'           => $this->is_served,
            'is_paid'             => $this->is_paid,
            'is_completed'        => $this->is_completed,
            'note'                => $this->note,
            'reason'              => $this->reason,
            'total_dish'          => $this->total_dish,
            'total_eater'         => $this->total_eater,
            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
            'stage'               => 'remote',
            '$isDirty'            => false,
            '$isNew'              => false,
            'items'               => ( new OrderItemsCollection($this->whenLoaded('items')) )
                ->using([ 'parent_uuid' => null ]),
            'promotions'          => ( new PromotionCollection($this->whenLoaded('promotions')) ),
            $this->mergeWhen(
                $this->resource->relationLoaded('table'),
                function () {
                    return [
                        'table_uuid' => $this->table->uuid ?? null,
                        'table_name' => $this->table->name ?? '',
                        'area_name'  => $this->table->area->name ?? '',
                    ];
                }
            ),
            $this->mergeWhen(
                $this->resource->relationLoaded('customer'),
                function () {
                    return [
                        'customer_uuid' => $this->customer->uuid ?? null,
                        'customer_name' => $this->customer->name ?? '',
                        'customer_code' => $this->customer->code ?? '',
                    ];
                }
            ),
            $this->mergeWhen(
                $this->whenLoaded('creator'),
                [
                    'creator_uuid'         => $this->creator->uuid ?? '',
                    'creator_name'         => $this->creator->name ?? '',
                    'creator_display_name' => $this->creator->display_name ?? '',
                ]
            ),
            $this->merge($this->using),
        ];
    }
}
