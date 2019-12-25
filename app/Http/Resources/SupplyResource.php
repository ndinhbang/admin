<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplyResource extends JsonResource
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
            'uuid'           => $this->uuid,
            'name'           => $this->name,
            'price_in'       => $this->price_in,
            'price_avg_in'       => $this->price_avg_in,
            $this->mergeWhen($this->resource->relationLoaded('unit'), [
                'unit_uuid' => isset($this->unit->uuid) ? $this->unit->uuid : '',
                'unit_name' => isset($this->unit->name) ? $this->unit->name : '',
            ]),
            $this->mergeWhen($this->resource->relationLoaded('stocks'), function () {
                return [
                    'stocks'       => $this->stocks,
                    'remain_stock' => $this->stocks->sum('pivot.remain'),
                ];
            }),
            'quantity_total' => isset($this->quantity_total) ? $this->quantity_total : 0,
            'remain_total'   => isset($this->remain_total) ? $this->remain_total : 0,
            'min_stock'      => $this->min_stock,
            'quantity'       => $this->whenPivotLoaded('product_supply', function () {
                return $this->pivot->quantity;
            }),
            'created_at'       => $this->created_at,
        ];
    }
}
