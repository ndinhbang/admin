<?php

namespace App\Http\Resources;

use App\Traits\UsingAdditionalData;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PromotionCollection extends ResourceCollection
{
    use UsingAdditionalData;

    public $collects = PromotionResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(
            function (PromotionResource $resource) use ($request) {
                return $resource->using($this->using)->toArray($request);
            }
        )->all();
    }
}
