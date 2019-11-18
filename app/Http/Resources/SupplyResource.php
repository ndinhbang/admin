<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplyResource extends JsonResource
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
            'uuid'     => $this->uuid,
            'name'     => $this->name,
            'price_in' => $this->price_in,
            'quantity' => $this->whenPivotLoaded('product_supply', function () {
                return $this->pivot->quantity;
            }),
        ];
    }
}
