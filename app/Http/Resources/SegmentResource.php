<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SegmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid'           => $this->uuid,
            'name'           => $this->name,
            'desc'           => $this->desc,
            'conditions'     => $this->conditions,
            'fixedCustomers' => $this->whenLoaded('fixedCustomers'),
        ];
    }
}
