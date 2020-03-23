<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed uuid
 * @property mixed name
 */
class PosCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param    \Illuminate\Http\Request    $request
     * @return array
     */
    public function toArray( $request )
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'type' => $this->type,
            'is_topping' => $this->is_topping,
            'products' => PosProductResource::collection($this->whenLoaded('products'))
        ];
    }
}
