<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class SegmentCustomerResource extends JsonResource
{

    public static $wrap = 'data';

    public function toArray($request)
    {
        return [
            'uuid'          => $this->uuid,
            'code'          => $this->code,
            'name'          => $this->name,
            'unsigned_name' => $this->unsigned_name,
            'contact_name'  => $this->contact_name,
            'gender'        => $this->gender,
//            'birth_month'         => $this->birth_month,
//            'birth_day'           => $this->birth_day,
//            'address'             => $this->address,
            'is_corporate'  => $this->is_corporate,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'tax_code'      => $this->tax_code,
//            'note'                => $this->note,
            'type'          => $this->type,
//            'total_amount'        => $this->total_amount,
//            'total_debt'          => $this->total_debt,
//            'total_return_amount' => $this->total_return_amount,
//            'last_order_at'       => $this->last_order_at,
//            'created_at'          => $this->created_at,
//            'updated_at'          => $this->updated_at,
        ];
    }
}
