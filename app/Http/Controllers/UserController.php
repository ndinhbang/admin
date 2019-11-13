<?php

namespace App\Http\Controllers;

use App\Filters\UserFilter;
use Illuminate\Http\Request;

class UserController extends Controller
{

    protected $avatar_path = 'images/users/';

    /**
     * Display a listing of the resource.
     *
     * @param UserFilter $filters
     * @return \Illuminate\Http\Response
     */
    public function index(UserFilter $filters)
    {

    }

    public function current(Request $request)
    {
        // current logged in user
        $user = $request->user();

        return response()->json([
            'user'        => $user->attributesToArray()
        ]);
    }
}
