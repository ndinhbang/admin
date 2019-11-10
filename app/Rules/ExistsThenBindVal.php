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

    /**
     * {@inheritDoc}
     */
    public function __construct($modelClass, $column)
    {
        $this->model = app($modelClass);
        $this->column = $column;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     * @return bool
     * @throws \ReflectionException
     */
    public function passes($attribute, $value)
    {
//        \DB::enableQueryLog();
        $record = $this->model->where($this->column, $value)->first();
//        dump($record, \Db::getQueryLog());
        if (is_null($record)) {
            return false;
        }
        // bind value to ioc
        app()->instance(strtolower(getClassShortName($record)), $record);

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
