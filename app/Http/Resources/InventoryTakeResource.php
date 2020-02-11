<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InventoryTakeResource extends JsonResource
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
            'uuid' => isset($this->uuid) ? $this->uuid : null,
            'code' => $this->code,
            $this->mergeWhen($this->resource->relationLoaded('creator'), [
                'creator_uuid' => isset($this->creator->uuid) ? $this->creator->uuid : null,
                'creator_name' => isset($this->creator->display_name) ? $this->creator->display_name : null,
            ]),
            'supplies' => InventorySupplyResource::collection($this->whenLoaded('supplies')),

            'on_date' => $this->on_date,
            'status' => $this->status,
            'note' => $this->note,
            'user_id' => $this->user_id,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
