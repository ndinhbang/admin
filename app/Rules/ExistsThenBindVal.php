<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

//use Illuminate\Validation\Rules\DatabaseRule;
class ExistsThenBindVal implements Rule
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

    protected $name;

    /**
     * {@inheritDoc}
     */
    public function __construct($modelClass, $column, $name = null)
    {
        $this->model  = app($modelClass);
        $this->column = $column;
        $this->name   = $name;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     * @throws \ReflectionException
     */
    public function passes($attribute, $value)
    {
        $record = $this->model->where($this->column, $value)
            ->first();
        if ( is_null($record) ) {
            return false;
        }
        $name = $this->name ?? strtolower(getClassShortName($record));
        // bind value to ioc
        app()->instance($name, $record);
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute not exist';
    }

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     */
    public function __toString()
    {
        return rtrim(sprintf('existsThenBindVal:%s,%s',
            $this->model,
            $this->column
        ), ',');
    }
}
