<?php

namespace App\Http\Middleware;

use App\Models\Place;
use Closure;

class BindCurrentPlaceIfExists
{
    /**
     * throw Exception if X-Place-Id not set
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $uuid = $request->header('X-Place-Id');
        $place = Place::where('uuid', $uuid)->first();

        // return error if place is not exists in case of requiring place uuid
        if (getBindVal('_requirePlace') && is_null($place)) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Bad request'], 400)
                : response('Bad request', 400);
        }

        // make current place instance available as a global
        app()->instance('_currentPlace', $place);

        return $next($request);
    }
}
