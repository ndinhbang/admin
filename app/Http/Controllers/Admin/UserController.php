<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Role;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param UserFilter $filters
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // \DB::enableQueryLog();
        $account = User::where(function ($query) use ($request) {
                if($request->keyword) {
                    $query->orWhere('name', 'like', '%'.$request->keyword.'%');
                    $query->orWhere('email', 'like', '%'.$request->keyword.'%');
                    $query->orWhere('phone', 'like', '%'.$request->keyword.'%');
                }
            })
            ->with('places')
            ->with('roles')
            ->orderBy('id', 'desc')->paginate($request->get('per_page', 8));
        // dump(\DB::getQueryLog());

        return $account->toJson();
    }

    public function store(UserRequest $request)
    {
        $user = DB::transaction(function () use ($request) {
            $arr = $request->all();
            $arr['uuid'] = nanoId();
            $arr['password'] = \Hash::make($request->password);
            // create user
            $user = User::create($arr);

            return $user;
        }, 5);

        return response()->json([
            'message'  => 'Thêm chủ quản thành công!',
            'employee' => $user,
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $user->display_name = $request->display_name;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        $user->status = $request->status;

        if ($request->password) {
            $user->password = \Hash::make($request->password);
        }

        $user->save();

        return response()->json(['message' => 'Cập nhật thông tin tài khoản thành công!', 'user' => $user]);
    }
}
