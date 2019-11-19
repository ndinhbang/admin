<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PosProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $stateArr = config('default.orders.state');

        return [
            'uuid'       => $this->uuid,
            'name'       => $this->name,
            'price'      => $this->price,
            'price_sale' => $this->price_sale,
            'is_hot'     => $this->is_hot,
            'thumbnail'  => config('app.media_url') . ($this->thumbnail ? '/products/' . $this->thumbnail : ''),
            'supplies'   => SupplyResource::collection($this->whenLoaded('supplies')),
            $this->mergeWhen($this->resource->pivot && $this->resource->pivot->getTable() === 'order_items', [
                'quantity'        => $this->pivot->quantity ?? 0,
                'total_price'     => $this->pivot->total_price ?? 0,
                'reason'          => $this->pivot->reason ?? '',
                'note'            => $this->pivot->note ?? '',
                'batch'           => $this->pivot->batch ?? 0,
                'is_canceled'     => $this->pivot->is_canceled ?? false,
                'is_served'       => $this->pivot->is_served ?? false,
                'is_done'         => $this->pivot->is_done ?? false,
                'state'           => $this->pivot->state ?? 0,
                'state_name'      => $stateArr[$this->pivot->state ?? 0]['name'],
                'next_state'      => $this->pivot && $this->pivot->state ? $this->pivot->state + 1 : 1,
                'next_state_name' => $stateArr[$this->pivot && $this->pivot->state ? $this->pivot->state + 1 : 1]['name'],
            ]),
        ];
    }
}
