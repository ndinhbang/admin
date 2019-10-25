<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function changePassword(ProfileRequest $request)
    {
        $user = $request->user();

        if (!Hash::check(request('current_password'), $user->password)) {
            return response()->json(['errors' => ['current_password' => ['Mặt khẩu cũ không khớp! Vui lòng thử lại!']]], 422);
        }

        $user->password = Hash::make(request('new_password'));
        $user->save();

        return response()->json(['message' => 'Mật khẩu của bạn đã được thay đổi thành công!']);
    }
}
