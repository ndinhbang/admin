<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AreaResource extends JsonResource
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
            'uuid'   => $this->uuid,
            'name'   => $this->name,
            'tables' => TableResource::collection($this->whenLoaded('tables')),
        ];
    }
}
