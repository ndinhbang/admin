<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Bouncer;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        // lay vi tri co level cao nhat cua user
        $maxRoleLevel = $user->roles()->max('level');
        // chi cho phep nguoi dung phan cac vi tri co level thap hon
        $roles = Bouncer::role()
            ->where('level', '<', $maxRoleLevel)
            ->get();

        return response()->json($roles);
    }
}
