<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class GdArrayExists implements Rule
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

    protected $attributeName;

    protected $globalName;

    protected $mergeFieldName = null;

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function __construct($modelClassName, $globalName = null, $column = null, $attributeName = null)
    {
        $this->model         = app($modelClassName); // resolve model class
        $this->column        = $column ?? $this->model->getRouteKeyName();
        $this->attributeName = $attributeName ?? $this->column;
        $this->globalName    = $globalName ?? ( '__' . Str::camel(class_basename($modelClassName)) );
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
        if ( is_array($value) ) {
            $uuids = data_get($value, '*.' . $this->column);
            if ( $this->mergeFieldName ) {
                $uuids = array_merge(
                    $uuids,
                    data_get($value, '*.' . $this->mergeFieldName . '.*.' . $this->column)
                );
            }
            $uuids   = array_unique($uuids);
            $records = $this->model->whereIn($this->column, $uuids)->get();
            if ( $records->count() != count($uuids) ) {
                return false;
            }
            if ( app()->offsetExists($this->globalName) ) {
                throw new \Exception("{$this->globalName} is already registered in container");
            }
            $keyed = $records->keyBy($this->model->getRouteKeyName());
            // bind value to app container
            app()->instance($this->globalName, $keyed);
            return true;
        }
        return false;
    }

    public function mergeFromField($fieldName)
    {
        $this->mergeFieldName = $fieldName;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute is malformed';
    }
}
