<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uuid'          => $this->uuid,
            'code'          => $this->code,
            $this->mergeWhen($this->resource->relationLoaded('category'), [
                'category_uuid' => $this->category->uuid,
                'category_name' => $this->category->name,
            ]),
            'supplies'      => SupplyResource::collection($this->whenLoaded('supplies')),
            'name'          => $this->name,
            'opened'        => $this->opened,
            'can_stock'     => $this->can_stock,
            'is_hot'        => $this->is_hot,
            'position'      => $this->position,
            'price'         => $this->price,
            'state'         => $this->state,
            'thumbnail'     => $this->thumbnail ? '/products/' . $this->thumbnail : '',
            'thumbnailFile' => null,
            'updated_at'    => $this->updated_at,
        ];
    }
}
