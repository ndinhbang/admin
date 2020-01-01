<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'uuid'                     => $this->uuid,
            'quantity'                 => $this->quantity,
            'total_price'              => $this->total_price,
            'simple_price'             => $this->simple_price,
            'children_price'           => $this->children_price,
            'total_buying_price'       => $this->total_buying_price,
            'total_buying_avg_price'   => $this->total_buying_avg_price,
            'canceled'                 => $this->canceled,
            'completed'                => $this->completed,
            'delivering'               => $this->delivering,
            'done'                     => $this->done,
            'doing'                    => $this->doing,
            'accepted'                 => $this->accepted,
            'pending'                  => $this->pending,
            'note'                     => $this->note,
            'discount_amount'          => $this->discount_amount,
            'children_discount_amount' => $this->children_discount_amount,
            'discount_order_amount'    => $this->discount_order_amount,
            '$isDirty'                 => false,
            $this->mergeWhen($this->whenLoaded('product'), [
                'product_uuid' => $this->product->uuid,
                'product'      => new PosProductResource($this->product),
            ]),
            $this->mergeWhen($this->whenLoaded('children'), [
                'children' => OrderItemResource::collection($this->children),
            ]),
        ];
    }
}
