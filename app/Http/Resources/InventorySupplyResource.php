<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InventorySupplyResource extends JsonResource
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
            'uuid'     => $this->uuid,
            'name'     => $this->name,
            'quantity' => $this->whenPivotLoaded('inventory', function () {
                return $this->pivot->quantity;
            }),
            'price_pu' => $this->whenPivotLoaded('inventory', function () {
                return $this->pivot->price_pu;
            }),
            'total_price' => $this->whenPivotLoaded('inventory', function () {
                return $this->pivot->total_price;
            }),
        ];
    }
}
