<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PosProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray( $request )
    {
        $stateArr      = config('default.orders.state');
        $enableKitchen = config('default.pos.enable_kitchen');
        return [
            'uuid'       => $this->uuid,
            'name'       => $this->name,
            'price'      => $this->price,
            'price_sale' => $this->price_sale,
            'is_hot'     => $this->is_hot,
            'state'      => $this->state,
            'opened'     => $this->opened,
            'thumbnail'  => ( $this->thumbnail ? config('app.media_url') . '/products/' . $this->thumbnail : '' ),
            'supplies'   => SupplyResource::collection($this->whenLoaded('supplies')),
            'items'      => OrderItemResource::collection($this->whenLoaded('items')),
            $this->mergeWhen($this->resource->relationLoaded('category'), function () {
                return [
                    'category_uuid' => $this->category->uuid,
                    'category_name' => $this->category->name,
                ];
            }),
            'created_at' => $this->created_at,
        ];
    }
}
