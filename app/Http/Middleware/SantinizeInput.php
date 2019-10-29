<?php

namespace App\Http\Middleware;

use Closure;
use GrahamCampbell\Binput\Binput;

class SantinizeInput
{
    /**
     * The Bouncer instance.
     *
     * @var \GrahamCampbell\Binput\Binput
     */
    protected $binput;

    /**
     * Constructor.
     *
     * @param Binput $binput
     */
    public function __construct(Binput $binput)
    {
        $this->binput = $binput;
    }

    /**
     * Set the proper Bouncer scope for the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->merge($this->binput->all());

        return $next($request);
    }
}
