<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EmployeeRequest;

class EmployeeController extends Controller
{

    protected $avatar_path = 'medias/avatars/';
    
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

    public function changePassword(EmployeeRequest $request)
    {
        $user = $request->user();

        if (!Hash::check(request('current_password'), $user->password)) {
            return response()->json(['errors' => ['current_password' => ['Mặt khẩu cũ không khớp! Vui lòng thử lại!']]], 422);
        }

        $user->password = Hash::make(request('new_password'));
        $user->save();

        return response()->json(['message' => 'Mật khẩu của bạn đã được thay đổi thành công!']);
    }

    public function store(EmployeeRequest $request)
    {
        $user = $request->user();

        $place = \App\Models\Place::find(request()->place_id);

        if (!$place) {
            return response()->json(['errors' => ['' => ['Không tìm thấy thông tin cửa hàng!']]], 422);
        }

        $employee = new \App\User;
        $employee->display_name = request()->display_name;
        $employee->name = request()->name;
        $employee->email = request()->email;
        $employee->phone = request()->phone;
        $employee->password = \Hash::make(request()->password);

        $employee->save();

        // thêm nhân viên vào cửa hàng
        $employee->places()->attach($place->id);

        return response()->json(['message' => 'Thêm nhân viên thành công!', 'employee' => $employee]);
    }

    public function update(EmployeeRequest $request, $id)
    {
        $user = $request->user();

        $employee = \App\User::find($id);

        if (!$employee) {
            return response()->json(['errors' => ['' => ['Không tìm thấy thông tin nhân viên!']]], 422);
        }

        $employee->display_name = request('display_name');

        if(request()->password)
        {
            $employee->password = \Hash::make(request()->password);
        }
        
        $employee->save();

        return response()->json(['message' => 'Cập nhật thông tin nhân viên thành công!', 'employee' => $employee]);
    }

    public function updateAvatar(EmployeeRequest $request)
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
