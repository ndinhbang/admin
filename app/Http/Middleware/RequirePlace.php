<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequirePlace
{
    protected $except = [
        'api/auth*',
        'api/place*',
        'api/roles*',
        'api/admin*',
        'api/profile*',
        'broadcasting/auth',
        'print/preview*',
        'print/report*',
        '_debugbar/*',
        'websockets*',
    ];

    /**
     * throw Exception if X-Place-Id not set
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure                  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( $this->inExceptArray($request) ) {
            return $next($request);
        }
        if ( is_null($uuid = $request->header('X-Place-Id')) ) {
            return $request->expectsJson()
                ? response()->json([ 'message' => 'Bad request' ], 400)
                : response('Bad request', 400);
        }
        // mark require place
        app()->instance('__requirePlace', true);
        return $next($request);
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param  Request  $request
     * @return bool
     */
    protected function inExceptArray(Request $request)
    {
        if ( app()->isLocal() ) {
            $this->except[] = 'telescope*';
//            $this->except[] = 'test*';
        }
        foreach ( $this->except as $except ) {
            if ( $except !== '/' ) {
                $except = trim($except, '/');
            }
            if ( $request->fullUrlIs($except) || $request->is($except) ) {
                return true;
            }
        }
        return false;
    }
}
