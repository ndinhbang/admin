<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SegmentResource extends JsonResource
{
    public static $wrap = 'data';
    public $additional = ['message' => ''];

    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid ?? null,
            'place_id' => $this->place_id ?? null,
            'title' => $this->title ?? null,
            'description' => $this->description ?? null,
            'customers' => SegmentCustomerResource::collection($this->whenLoaded('customers')),
            'criteria' => CriterionResource::collection($this->whenLoaded('criteria'))
        ];
    }
}
