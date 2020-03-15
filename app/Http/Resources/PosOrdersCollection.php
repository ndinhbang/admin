<?php

namespace App\Http\Resources;

use App\Traits\UsingAdditionalData;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PosOrdersCollection extends ResourceCollection
{
    use UsingAdditionalData;
    /**
     * The resource that this resource collects.
     * @var string
     */
    public $collects = 'App\Http\Resources\PosOrderResource';

    /**
     * Transform the resource collection into an array.
     * @link https://github.com/laravel/framework/issues/23826
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray( $request )
    {
        return $this->collection->map(function ( PosOrderResource $resource ) use ( $request ) {
            return $resource->using($this->using)->toArray($request);
        })->all();
    }
}
