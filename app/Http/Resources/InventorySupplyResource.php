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
            'qty_export' => $this->whenPivotLoaded('inventory', function () {
                return $this->pivot->qty_export;
            }),
            'qty_import' => $this->whenPivotLoaded('inventory', function () {
                return $this->pivot->qty_import;
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
