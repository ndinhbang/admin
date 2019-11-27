<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PosProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $stateArr      = config('default.orders.state');
        $enableKitchen = config('default.pos.enable_kitchen');
        return [
            'uuid'       => $this->uuid,
            'name'       => $this->name,
            'price'      => $this->price,
            'price_sale' => $this->price_sale,
            'is_hot'     => $this->is_hot,
            'thumbnail'  => config('app.media_url') . ( $this->thumbnail ? '/products/' . $this->thumbnail : '' ),
            'supplies'   => SupplyResource::collection($this->whenLoaded('supplies')),
            $this->mergeWhen($this->resource->relationLoaded('category'), [
                'category_uuid' => $this->category->uuid,
                'category_name' => $this->category->name,
            ]),
            $this->mergeWhen(
                $this->resource->pivot
                && $this->resource->pivot->getTable() === 'order_items',
                [
                    'quantity'    => $this->pivot->quantity ?? 0,
                    'pending'     => $this->pivot->pending ?? 0,
                    'accepted'    => $this->pivot->accepted ?? 0,
                    'doing'       => $this->pivot->doing ?? 0,
                    'done'        => $this->pivot->done ?? 0,
                    'delivering'  => $this->pivot->delivering ?? 0,
                    'completed'   => $this->pivot->completed ?? 0,
                    'canceled'    => $this->pivot->canceled ?? 0,
                    'total_price' => $this->pivot->total_price ?? 0,
//                    'reason'        => $this->pivot->reason ?? '',
                    'note'        => $this->pivot->note ?? '',
//                    'batch'         => $this->pivot->batch ?? 0,
//                    'is_canceled'   => $this->pivot->is_canceled ?? false,
//                    'is_served'     => $this->pivot->is_served ?? false,
//                    'is_done'       => $this->pivot->is_done ?? false,
//                    'state'         => $this->pivot->state ?? 0,
//                    '_currentState' => currentState($this->pivot->state ?? 0),
//                    '_nextState'    => nextState($this->pivot->state ?? 0),
                ]
            ),
        ];
    }
}
