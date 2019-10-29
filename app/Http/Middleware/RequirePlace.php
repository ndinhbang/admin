<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class RequirePlace
{
    protected $except = [
        'api/auth/*',
        'api/place/*',
    ];

    /**
     * throw Exception if X-Place-Id not set
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->inExceptArray($request)) {
            return $next($request);
        }

        if (is_null($uuid = $request->header('X-Place-Id'))) {
            if (!$request->expectsJson()) {
                return response('Bad request', 400);
            }
            return response()->json(['message' => 'Bad request'], 400);
        }

        // pass down place_uuid
        $request->merge(['place_uuid' => $uuid]);

        return $next($request);
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param Request $request
     * @return bool
     */
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
