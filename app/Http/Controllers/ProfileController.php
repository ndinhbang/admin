<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{

    protected $avatar_path = 'medias/avatars/';

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

    public function updateProfile(ProfileRequest $request)
    {
        $user = $request->user();

        $user->display_name = request('display_name');
        $user->name = request('name');
        $user->phone = request('phone');
        $user->email = request('email');
        $user->save();

        return response()->json(['message' => 'Cập nhật thông tin tài khoản thành công!', 'user' => $user]);
    }

    public function updateAvatar(ProfileRequest $request)
    {
        $user = $request->user();

        if ($user->avatar && \File::exists($this->avatar_path . $user->avatar)) {
            \File::delete($this->avatar_path . $user->avatar);
        }

        $extension = $request->file('avatar')->getClientOriginalExtension();
        $filename  = $user->id.'-'.$user->name. "-goido.net.";

        $img       = \Image::make($request->file('avatar'));

        $filePath = $this->avatar_path . $filename . $extension;
        $img->fit(200, 200);
        $img->save($filePath);

        $user->avatar = $filename . $extension;
        $user->save();

        return response()->json(['message' => 'Cập nhật ảnh đại diện thành công!', 'user' => $user]);
    }
}
