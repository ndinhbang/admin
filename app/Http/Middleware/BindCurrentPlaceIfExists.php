<?php

namespace App\Http\Middleware;

use App\Models\Place;
use Closure;

class BindCurrentPlaceIfExists
{
    /**
     * throw Exception if X-Place-Id not set
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure                  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // return error if place is not exists in case of requiring place uuid
        if ( ( is_null($uuid = $request->header('X-Place-Id'))
                || is_null($place = Place::where('uuid', $uuid)
                    ->first()) )
            && getBindVal('__requirePlace') ) {
            return $request->expectsJson()
                ? response()->json([ 'message' => 'Bad request' ], 400)
                : response('Bad request', 400);
        }
        // make current place instance available as a global
        app()->instance('__currentPlace', $place ?? null);
        return $next($request);
    }
}
