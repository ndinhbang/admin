<?php

namespace App\Traits;

trait UsingAdditionalData
{
    protected $using = [];

    /**
     * Add additional data to the API resource.
     *
     * @param  array  $data
     * @return $this
     */
    public function using(array $data)
    {
        $this->using = $data;
        return $this;
    }
}
