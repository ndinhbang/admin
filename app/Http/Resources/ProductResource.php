<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uuid'          => $this->uuid,
            'name'          => $this->name,
            'price'         => $this->price,
            'is_hot'        => $this->is_hot,
            'thumbnail'     => config('app.media_url') . ( $this->thumbnail ? '/products/' . $this->thumbnail : '' ),
            'thumbnailFile' => null,
            'code'          => $this->code,
            'opened'        => $this->opened,
            'can_stock'     => $this->can_stock,
            'price_by_time' => $this->price_by_time,
            'position'      => $this->position,
            'state'         => $this->state,
            'updated_at'    => $this->updated_at,
            'supplies'      => SupplyResource::collection($this->whenLoaded('supplies')),
            $this->mergeWhen($this->resource->relationLoaded('category'), [
                'category_uuid' => $this->category->uuid,
                'category_name' => $this->category->name,
            ]),
        ];
    }
}
