<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
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
            'uuid'               => $this->uuid,
            'code'               => $this->code,
            'title'              => $this->title,
            'imported_at'        => $this->imported_at,
            'amount'             => $this->amount,
            'payment_method'     => $this->payment_method,
            'note'               => $this->note,
            'type'               => $this->type,
            'state'              => $this->state,
            'order_id'           => $this->order_id,
            'inventory_order_id' => $this->inventory_order_id,
            'updated_at'         => $this->updated_at,
            'deleted_at'         => $this->deleted_at,
            $this->mergeWhen($this->resource->relationLoaded('payer_payee'), [
                'payer_payee_uuid' => $this->payer_payee->uuid ?? '',
                'payer_payee_name' => $this->payer_payee->name ?? '',
                'payer_payee_code' => $this->payer_payee->code ?? '',
                'payer_payee_type' => $this->payer_payee->type ?? '',
                'payer_payee' => $this->payer_payee,
            ]),
            $this->mergeWhen($this->resource->relationLoaded('category'), [
                'category_uuid' => $this->category->uuid ?? null,
                'category_name' => $this->category->name ?? null,
            ]),
            $this->mergeWhen($this->resource->relationLoaded('creator'), [
                'creator_uuid' => $this->creator->uuid ?? null,
                'creator_name' => $this->creator->display_name ?? null,
            ]),
        ];
    }
}
