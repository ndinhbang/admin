<?php

namespace App\Http\Resources;

use App\Traits\UsingAdditionalData;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed uuid
 * @property mixed name
 * @property mixed code
 * @property mixed type
 * @property mixed state
 * @property mixed applied
 * @property mixed required_code
 * @property mixed is_limited
 * @property mixed limit_qty
 * @property mixed applied_qty
 * @property mixed from
 * @property mixed to
 * @property mixed stats
 * @property mixed rule
 * @property mixed customers
 * @property mixed place_uuid
 * @property mixed note
 * @property mixed segments
 * @property mixed created_at
 * @property mixed updated_at
 */
class PromotionResource extends JsonResource
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
            'uuid'            => $this->uuid,
            'name'            => $this->name,
            'code'            => $this->code,
            'type'            => $this->type,
            'state'           => $this->state,
            'applied'         => $this->applied,
            'required_code'   => $this->required_code,
            'is_limited'      => $this->is_limited,
            'limit_qty'       => $this->limit_qty,
            'applied_qty'     => $this->applied_qty,
            'from'            => $this->from,
            'to'              => $this->to,
            'stats'           => $this->stats,
            'rule'            => $this->rule,
            'customers'       => $this->customers,
            'segments'        => $this->segments,
            'note'            => $this->note,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
            'place_uuid'      => $this->place_uuid,
            'discount_amount' => $this->whenPivotLoaded(
                'promotion_detail',
                function () {
                    return $this->pivot->discount_amount;
                }
            ),
        ];
    }
}
