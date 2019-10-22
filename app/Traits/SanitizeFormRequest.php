<?php

namespace App\Traits;

trait SanitizeFormRequest
{
    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Sanitize data from the request before validation
        $this->merge(app('binput')->all());
    }
}
