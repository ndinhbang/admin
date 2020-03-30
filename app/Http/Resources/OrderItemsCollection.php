<?php

namespace App\Http\Resources;

use App\Traits\UsingAdditionalData;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderItemsCollection extends ResourceCollection
{
    use UsingAdditionalData;
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'App\Http\Resources\OrderItemResource';

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(
            function (OrderItemResource $resource) use ($request) {
                return $resource->using($this->using)->toArray($request);
            }
        )->all();
    }
}
