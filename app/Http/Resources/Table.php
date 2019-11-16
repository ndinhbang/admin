<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Table extends JsonResource
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
            'uuid'        => $this->uuid,
            'name'        => $this->name,
            'area'        => $this->when($this->resource->relationLoaded('area'), $this->area),
            'is_avaiable' => $this->order_id > 0 ? true : false
        ];
    }
}
