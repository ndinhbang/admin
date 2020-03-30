<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class CriterionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid'       => $this->uuid ?? null,
//            'segment_id' => $this->segment_id,
//            'place_id'   => $this->place_id,
            'operator'   => $this->operator,
            'property'   => $this->property,
            'value'      => $this->value,
            'property_name' => $this->property_name,
        ];
    }
}
