<?php

namespace App\Http\Middleware;

use App\Models\Place;
use Closure;
use Illuminate\Support\Facades\App;

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
        if ($request->requirePlace && is_null($place)) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Bad request'], 400)
                : response('Bad request', 400);
        }

        if (!App::offsetExists('currentPlace')) {
            // make current place instance available as a global
            App::instance('currentPlace', $place);
        }

        return $next($request);
    }
}
