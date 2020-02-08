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
                    // 'remain_stock' => $this->stocks->sum('pivot.remain'),
                ];
            }),
            'remain'      => $this->remain,
            'min_stock'      => $this->min_stock,
            'quantity'       => $this->whenPivotLoaded('product_supply', function () {
                return $this->pivot->quantity;
            }),
            'remain_on_quantity'       => $this->whenPivotLoaded('product_supply', function () {
                return $this->pivot->quantity ? round($this->remain / $this->pivot->quantity, 2) : 0;
            }),
            'created_at'       => $this->created_at,
        ];
    }
}
