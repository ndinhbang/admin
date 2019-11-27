<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed name
 * @property mixed uuid
 * @property mixed orders_count
 */
class TableResource extends JsonResource
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
            'uuid'         => $this->uuid,
            'name'         => $this->name,
            'orders_count' => $this->when(isset($this->orders_count), $this->orders_count),
            'area'         => new AreaResource($this->whenLoaded('area')),
        ];
    }
}
