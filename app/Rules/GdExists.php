<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class GdExists implements Rule
{
    /**
     * @var string
     */
    protected $model;

    /**
     * The column to check on.
     *
     * @var string
     */
    protected $column;

    protected $globalName;

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function __construct($modelClassName, $column = null, $globalName = null)
    {
        $this->model      = app($modelClassName); // resolve model class
        $this->column     = $column ?? $this->model->getRouteKeyName();
        $this->globalName = $globalName ?? ( '__' . Str::camel(class_basename($modelClassName)) );
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     * @throws \Exception
     */
    public function passes($attribute, $value)
    {
        if ( is_null($record = $this->model->where($this->column, $value)->first()) ) {
            return false;
        }
        if ( app()->offsetExists($this->globalName) ) {
            throw new \Exception("{$this->globalName} is already registered in container");
        }
        // bind value to app container
        app()->instance($this->globalName, $record);
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute không tồn tại';
    }
}
