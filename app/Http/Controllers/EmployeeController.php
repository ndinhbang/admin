<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $place = $user->places;

        $users = \App\User::select('users.*')
            ->join('place_user', 'place_user.user_id', '=', 'users.id')
            ->join('places', 'places.id', '=', 'place_user.place_id')
            ->where('places.id', request()->place_id)
            ->paginate(10);

        return $users->toJson();
    }
}
