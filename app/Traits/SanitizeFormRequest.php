<?php

namespace App\Traits;

trait SanitizeFormRequest
{
    /**
     * Sanitize data from the request before validation
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge(app('binput')->all());
    }
}
