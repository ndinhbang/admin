<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Silber\Bouncer\Bouncer;

class ScopeBouncer
{
    protected $except = [
        'api/auth*',
        'api/place*',
        'api/roles*',
    ];

    /**
     * The Bouncer instance.
     *
     * @var \Silber\Bouncer\Bouncer
     */
    protected $bouncer;

    /**
     * Constructor.
     *
     * @param \Silber\Bouncer\Bouncer $bouncer
     */
    public function __construct(Bouncer $bouncer)
    {
        $this->bouncer = $bouncer;
    }

    /**
     * Set the proper Bouncer scope for the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (is_null($uuid = $request->header('X-Place-Id'))
        || $this->inExceptArray($request)) {
            return $next($request);
        }
        if (is_null($place = \App\Models\Place::where('uuid', $uuid)->first())) {
            if (!$request->expectsJson()) {
                return response('Place not found', 404);
            }
            return response()->json(['message' => 'Place not found'], 404);
        }

        // add global scope for bouncer
        $this->bouncer->scope()->to($place->id);
        // pass down place
        $request->request->set('place', $place);

        return $next($request);
    }

    protected function inExceptArray(Request $request) {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return  false;
    }
}
