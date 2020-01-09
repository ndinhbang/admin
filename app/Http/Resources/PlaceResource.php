<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed uuid
 * @property mixed code
 * @property mixed title
 * @property mixed logo
 * @property mixed contact_name
 * @property mixed contact_phone
 * @property mixed contact_email
 * @property mixed address
 * @property mixed status
 * @property mixed expired_date
 * @property mixed config_print
 * @property mixed config_screen2nd
 * @property mixed print_templates
 */
class PlaceResource extends JsonResource
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
            'uuid'             => $this->uuid,
            'code'             => $this->code,
            'title'            => $this->title,
            'logo'             => $this->logo,
            'contact_name'     => $this->contact_name,
            'contact_phone'    => $this->contact_phone,
            'contact_email'    => $this->contact_email,
            'address'          => $this->address,
            'status'           => $this->status,
            'expired_date'     => $this->expired_date,
            'user'             => $this->whenLoaded('user'),
            'users'            => $this->whenLoaded('users'),
            'print_templates'  => $this->print_templates,
            'config_sale'      => $this->config_sale ?? config('default.config.sale'),
            'config_print_info'=> $this->print_info ?? config('default.print.info'),
            'config_print'     => $this->config_print ?? config('default.print.config'),
            'config_screen2nd' => [
//                'useImage' => $this->config_screen2nd['useImage'] ?? false,
//                'image'    => $this->config_screen2nd['image']
//                    ? config('app.media_url') . '/screen2nd/' . $this->config_screen2nd['image']
//                    : '',
            ],
        ];
    }
}
