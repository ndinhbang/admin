<?php

namespace App\Concerns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilter
{
    protected $request;
    protected $builder;

    public function __construct(FormRequest $request)
    {
        $this->request = $request;
    }

    /**
     * [apply description]
     * @param Builder $builder [description]
     * @return Builder
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $name => $value) {
            if (method_exists($this, $name)) {
                // call_user_func_array([$this, $name], array_filter([$value]));
                if (!empty($value) || $value == 0) {
                    $this->$name($value);
                } else {
                    $this->$name();
                }
            }
        }

        return $this->builder;
    }

    /**
     * Get request input. default is get all inputs
     *
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->request->all();
        }
        return $this->request->input($key, $default);
    }
}
