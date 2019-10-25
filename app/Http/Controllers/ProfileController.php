<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{

    protected $avatar_path = 'images/avatas/';

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

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $user->display_name = request('display_name');
        $user->save();

        return response()->json(['message' => 'Cập nhật thông tin tài khoản thành công!', 'user' => $user]);
    }

    public function updateAvatar(ProfileRequest $request)
    {
        $user = $request->user();

        if ($user->avatar && \File::exists($this->avatar_path . $profile->avatar)) {
            \File::delete($this->avatar_path . $user->avatar);
        }

        $extension = $request->file('avatar')->getClientOriginalExtension();
        $filename  = uniqid();
        $file      = $request->file('avatar')->move($this->avatar_path, $filename . "." . $extension);
        $img       = \Image::make($this->avatar_path . $filename . "." . $extension);
        $img->resize(200, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($this->avatar_path . $filename . "." . $extension);
        $user->avatar = $filename . "." . $extension;
        $user->save();

        return response()->json(['message' => 'Cập nhật ảnh đại diện thành công!', 'user' => $user]);
    }
}
