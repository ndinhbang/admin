<?php

namespace App\Http\Resources;

use App\Traits\UsingAdditionalData;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    use UsingAdditionalData;

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
//            'parent_id'                => $this->parent_id,
            'quantity'                 => $this->quantity,
            'printed_qty'              => $this->printed_qty,
            // so luong in
            'added_qty'                => 0,
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
            'discount_value'           => $this->discount_amount,
            'discount_type'            => 'đ',
            'children_discount_amount' => $this->children_discount_amount,
            'discount_order_amount'    => $this->discount_order_amount,
            'time_used'                => $this->time_used,
            'time_in'                  => $this->time_in,
            'time_out'                 => $this->time_out,
            'updated_at'               => $this->updated_at,
            '$isDirty'                 => false,
            '$isNew'                   => false,
            'is_remote'                => true,
            $this->mergeWhen($this->resource->relationLoaded('product'), function () {
                return [
                    'category_uuid' => $this->product->category->uuid ?? null,
                    'product_uuid'  => $this->product->uuid ?? null,
                    'product_name'  => $this->product->name ?? '',
                    'product_price' => $this->product->price ?? 0,
                    'price_by_time' => $this->product->price_by_time ?? false,
//                    'product'       => new PosProductResource($this->product),
                ];
            }),
            $this->mergeWhen($this->resource->relationLoaded('children'), function () {
                return [
                    'children' => ( new OrderItemsCollection($this->children) )->using([
                        'parent_uuid' => $this->uuid,
                    ]),
                ];
            }),
            $this->merge($this->using),
        ];
    }
}
