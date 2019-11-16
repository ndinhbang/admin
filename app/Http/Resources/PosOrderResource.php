<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PosOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $stateArr = config('default.orders.state');

        return [
            'uuid'         => $this->uuid,
            'code'         => $this->code,
            'state'        => $this->state,
            'state_name'   => $stateArr[$this->state ?? 0]['name'],
            'amount'       => $this->amount,
            'debt'         => $this->debt,
            'paid'         => $this->paid,
            'is_returned'  => $this->is_returned,
            'is_canceled'  => $this->is_canceled,
            'is_served'    => $this->is_served,
            'is_paid'      => $this->is_paid,
            'is_completed' => $this->is_completed,
            'note'         => $this->note,
            'reason'       => $this->reason,
        ];
    }
}
